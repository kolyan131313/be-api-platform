<?php declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PostListTest extends CustomApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testPostList(): void
    {
        $result = $this->sendAuthenticatedRequestAsSimpleUser('GET', '/api/posts');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('Content-type', 'application/json; charset=utf-8');
        $responseData = json_decode($result->getContent(), true);


//        $this->assertEquals($responseData['email'], $testEmail);
//        $this->assertEquals($responseData['code'], Response::HTTP_CREATED);
    }
}