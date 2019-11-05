<?php declare(strict_types=1);

namespace App\Tests;

use App\Entity\MediaObject;
use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use App\Repository\VerificationRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class VerificationRequestTest extends CustomApiTestCase
{
    /**
     * @return array
     */
    public function deniedStatusesForEdit(): array
    {
        return [
            ['status' => VerificationStatusEnum::APPROVED],
            ['status' => VerificationStatusEnum::DECLINED]
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUserInitiatingVerificationRequest(): void
    {
        $files = $this->getMediaObjectRepository()->findAll();
        /** @var MediaObject $firstMediaObject */
        $firstMediaObject = $files[0];
        $sourceLink = '/api/media-objects/' . $firstMediaObject->getId();

        $result = $this->sendAuthenticatedRequestAsSimpleUser(Request::METHOD_POST, '/api/verification-requests', [
            'json' => [
                'image' => $sourceLink,
                'message' => 'Test message'
            ]
        ]);

        static::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        static::assertResponseHeaderSame('Content-type', 'application/json; charset=utf-8');

        $responseData = json_decode($result->getContent(), true);

        $this->assertContains($sourceLink, $responseData);
        /** @var VerificationRequestRepository[] $mediaObject */
        $verificationRequest = $this->getVerificationRequestRepository()->find($responseData['id']);

        $this->assertEquals(VerificationStatusEnum::VERIFICATION_REQUESTED, $verificationRequest->getStatus());
        $this->assertEquals($firstMediaObject->getId(), $verificationRequest->getImage()->getId());
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUserCanEditVerificationRequest(): void
    {
        /** @var MediaObject[] $entities */
        $entities = $this->getMediaObjectRepository()->findAll();
        /** @var MediaObject $lastMediaObject */
        $lastMediaObject = $entities[count($entities) - 1];

        /** @var VerificationRequest $verificationRequest */
        $verificationRequest = $this->getVerificationRequestRepository()->findOneBy([
            'status' => VerificationStatusEnum::VERIFICATION_REQUESTED
        ]);

        $sourceLink = '/api/media-objects/' . $lastMediaObject->getId();

        $result = $this->sendAuthenticatedRequestAsSimpleUser(
            Request::METHOD_PUT,
            sprintf('/api/verification-requests/%d', $verificationRequest->getId()),
            [
                'json' => [
                    'image' => $sourceLink,
                    'message' => 'Test message 2'
                ]
            ]
        );

        static::assertResponseStatusCodeSame(Response::HTTP_OK);
        static::assertResponseHeaderSame('Content-type', 'application/json; charset=utf-8');

        $responseData = json_decode($result->getContent(), true);

        $this->assertContains($sourceLink, $responseData);
        /** @var VerificationRequestRepository[] $mediaObject */
        $verificationRequest = $this->getVerificationRequestRepository()->find($responseData['id']);

        $this->assertEquals(VerificationStatusEnum::VERIFICATION_REQUESTED, $verificationRequest->getStatus());
        $this->assertEquals($lastMediaObject->getId(), $verificationRequest->getImage()->getId());
    }

    /**
     * @dataProvider deniedStatusesForEdit
     *
     * @param string $status
     *
     * @throws TransportExceptionInterface
     */
    public function testUserCantEditInStatusApprove(string $status): void
    {
        /** @var VerificationStatusEnum $entitiy */
        $entity = $this->getVerificationRequestRepository()->findOneBy(['status' => $status]);
        /** @var MediaObject[] $entities */
        $entities = $this->getMediaObjectRepository()->findAll();
        /** @var MediaObject $firsMediaObject */
        $firsMediaObject = $entities[0];

        $sourceLink = '/api/media-objects/' . $firsMediaObject->getId();

        $this->sendAuthenticatedRequestAsSimpleUser(
            Request::METHOD_PUT,
            sprintf('/api/verification-requests/%d', $entity->getId()),
            [
                'json' => [
                    'image' => $sourceLink,
                    'message' => 'Test message'
                ]
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUserCantSeeListOfVerifications(): void
    {
        $this->sendAuthenticatedRequestAsSimpleUser(
            Request::METHOD_GET,
            '/api/verification-requests'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testAdminCanSeeListOfVerifications(): void
    {
        $this->sendAuthenticatedRequestAsAdmin(
            Request::METHOD_GET,
            '/api/verification-requests'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider deniedStatusesForEdit
     *
     * @param string $status
     *
     * @throws TransportExceptionInterface
     */
    public function testAdminCanSeeListOfVerificationsAndFilterByStatus(string $status): void
    {
        $results = $this->sendAuthenticatedRequestAsAdmin(
            Request::METHOD_GET,
            '/api/verification-requests',
            [
                'query' => ['status' => $status]
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        foreach ($results as $result) {
            $this->assertEquals($result['status'], $status);
        }
    }

    /**
     * @dataProvider deniedStatusesForEdit
     *
     * @throws TransportExceptionInterface
     */
//    public function testAdminCanApproveVerificationRequest(): void
//    {
//        /** @var VerificationRequest $verificationRequest */
//        $verificationRequest = $this->getVerificationRequestRepository()->findOneBy([
//            'status' => VerificationStatusEnum::VERIFICATION_REQUESTED
//        ]);
//
//        $this->sendAuthenticatedRequestAsAdmin(
//            Request::METHOD_PUT,
//            sprintf('/api/verification-requests/%d/approve', $verificationRequest->getId())
//        );
//
//        self::assertResponseStatusCodeSame(Response::HTTP_OK);
//
//        $updatedVerificationRequest = $this->getVerificationRequestRepository()->find($verificationRequest->getId());
//
//        $this->assertEquals(VerificationStatusEnum::APPROVED, $updatedVerificationRequest->getStatus());
//        $this->assertContains(UserRolesEnum::BLOGGER, $updatedVerificationRequest->getOwner()->getRoles());
//    }
}