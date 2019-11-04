<?php declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoginTest extends CustomApiTestCase
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
                'username' => UserFixtures::SIMPLE_USER_EMAIL,
                'password' => UserFixtures::DEFAULT_PASSWORD
            ],
            [
                'username' => UserFixtures::BLOGGER_USER_EMAIL,
                'password' => UserFixtures::DEFAULT_PASSWORD
            ],
            [
                'username' => UserFixtures::BLOGGER_USER_EMAIL,
                'password' => UserFixtures::DEFAULT_PASSWORD
            ],
        ];
    }

    /**
     * @dataProvider userCredentialsProvider
     *
     * @param string $username
     * @param string $password
     *
     * @throws TransportExceptionInterface
     */
    public function testLoginUserWithRightCredentials(string $username, string $password): void
    {
        $result = static::createClient()->request('POST', '/api/login', ['json' => [
            'username' => $username,
            'password' => $password
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-type', 'application/json');
        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testLoginUserWithWrongCredentials(): void
    {
        static::createClient()->request('POST', '/api/login', ['json' => [
            'username' => UserFixtures::WRONG_USER_EMAIL,
            'password' => 'wrong_password'
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('Content-type', 'application/json');
    }
}