<?php declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\PostFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PostTest extends CustomApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testPostList(): void
    {
        $result = $this->sendAuthenticatedRequestAsSimpleUser(Request::METHOD_GET, '/api/posts');

        static::assertResponseStatusCodeSame(Response::HTTP_OK);
        static::assertResponseHeaderSame('Content-type', 'application/json; charset=utf-8');
        $responseData = json_decode($result->getContent(), true);

        $this->assertCount(count(PostFixtures::POSTS), $responseData);
    }
}