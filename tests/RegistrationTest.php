<?php declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RegistrationTest extends CustomApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithRightCredentials(): void
    {
        $testEmail = 'test12@gmail.com';

        $result = static::createClient()->request('POST', '/api/register', ['json' => [
            'email' => $testEmail,
            'password' => UserFixtures::DEFAULT_PASSWORD,
            'lastName' => 'test',
            'firstName' => 'test'
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-type', 'application/json');
        $responseData = json_decode($result->getContent(), true);
        $this->assertEquals($responseData['email'], $testEmail);
        $this->assertEquals($responseData['code'], Response::HTTP_CREATED);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithExistingCredentials(): void
    {
        $requestDataSet = [
            'email' => 'test_api2@gmail.com',
            'password' => '777777',
            'lastName' => 'test2',
            'firstName' => 'test2'
        ];

        static::createClient()->request('POST', '/api/register', ['json' => $requestDataSet]);
        static::createClient()->request('POST', '/api/register', ['json' => $requestDataSet]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testRegistrationUserWithWrongCredentials(): void
    {
        $requestDataSet = ['email' => 'test_api2@gmail.com'];

        static::createClient()->request('POST', '/api/register', ['json' => $requestDataSet]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}