<?php declare(strict_types=1);

namespace App\Controller;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Enum\UserRolesEnum;
use App\Tests\CustomApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthControllerTest extends CustomApiTestCase
{
    /**
     * @return array
     */
    public function userRegistrationCredentialsProvider(): array
    {
        return [
            [
                [
                    'email' => 'test1@gmail.com',
                    'password' => UserFixtures::DEFAULT_PASSWORD,
                    'lastName' => 'test1',
                    'firstName' => 'test1'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function userRegistrationWrongEmailProvider(): array
    {
        return [
            [
                ['email' => 'test_api2@gmail.com']
            ]
        ];
    }

    /**
     * @dataProvider userRegistrationCredentialsProvider
     *
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithRightCredentials(array $data): void
    {
        $result = static::createClient()->request(Request::METHOD_POST, '/api/register', ['json' => $data]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertResponseHeaderSame('Content-type', 'application/json');

        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals($responseData['email'], $data['email']);
        $this->assertEquals($responseData['code'], Response::HTTP_CREATED);

        /** @var User[] $users */
        $users = $this->getUserRepository()->findBy(['email' => $data['email']]);
        $this->assertCount(1, $users);

        /** @var User $user */
        $user = $users[0];

        $this->assertContains(UserRolesEnum::SIMPLE_USER, $user->getRoles());
    }

    /**
     * @dataProvider userRegistrationCredentialsProvider
     *
     * @param array $data
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithExistingCredentials(array $data): void
    {
        static::createClient()->request(Request::METHOD_POST, '/api/register', ['json' => $data]);
        static::createClient()->request(Request::METHOD_POST, '/api/register', ['json' => $data]);

        static::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        /** @var User[] $users */
        $users = $this->getUserRepository()->findBy(['email' => $data['email']]);
        $this->assertCount(1, $users);
    }

    /**
     * @dataProvider userRegistrationWrongEmailProvider
     *
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithWrongCredentials(array $data): void
    {
        static::createClient()->request(Request::METHOD_POST, '/api/register', ['json' => $data]);

        static::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var User[] $users */
        $users = $this->getUserRepository()->findBy(['email' => $data['email']]);
        $this->assertCount(0, $users);
    }
}