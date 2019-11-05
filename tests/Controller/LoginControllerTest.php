<?php declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoginControllerTest extends CustomApiTestCase
{
    /**
     * Data for user login
     *
     * @return array
     */
    public function userCredentialsProvider(): array
    {
        return [
            [
                [
                    'username' => UserFixtures::SIMPLE_USER_EMAIL,
                    'password' => UserFixtures::DEFAULT_PASSWORD
                ]
            ],
            [
                [
                    'username' => UserFixtures::BLOGGER_USER_EMAIL,
                    'password' => UserFixtures::DEFAULT_PASSWORD
                ]
            ],
            [
                [
                    'username' => UserFixtures::BLOGGER_USER_EMAIL,
                    'password' => UserFixtures::DEFAULT_PASSWORD
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function userWrongCredentialsProvider(): array
    {
        return [
            [
                [
                    'username' => 'wrong_user@gmail.com',
                    'password' => 'wrong_password'
                ]
            ],
        ];
    }

    /**
     * @dataProvider userCredentialsProvider
     *
     * @param array $data
     * @throws TransportExceptionInterface
     */
    public function testLoginUserWithRightCredentials(array $data): void
    {
        $result = static::createClient()->request(Request::METHOD_POST, '/api/login', ['json' => $data]);

        static::assertResponseStatusCodeSame(Response::HTTP_OK);
        static::assertResponseHeaderSame('Content-type', 'application/json');
        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
    }

    /**
     * @dataProvider userWrongCredentialsProvider
     *
     * @param array $data
     *
     * @throws TransportExceptionInterface
     */
    public function testLoginUserWithWrongCredentials(array $data): void
    {
        static::createClient()->request(Request::METHOD_POST, '/api/login', ['json' => $data]);
        static::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        static::assertResponseHeaderSame('Content-type', 'application/json');
    }
}