<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MediaObject;
use App\Entity\User;
use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VerificationRequestFixtures extends Fixture implements OrderedFixtureInterface
{
    public const VERIFICATION_REQUESTS = [
        [
            'status' => VerificationStatusEnum::VERIFICATION_REQUESTED,
            'message' => 'Test message 1',
            'rejectionReason' => 'Bad request'
        ],
        [
            'status' => VerificationStatusEnum::VERIFICATION_REQUESTED,
            'message' => 'Test message 2',
            'rejectionReason' => 'Bad request'
        ],
        [
            'status' => VerificationStatusEnum::VERIFICATION_REQUESTED,
            'message' => 'Test message 3',
            'rejectionReason' => 'Bad request'
        ],
        [
            'status' => VerificationStatusEnum::APPROVED,
            'message' => 'Test message 4',
            'rejectionReason' => ''
        ],
        [
            'status' => VerificationStatusEnum::DECLINED,
            'message' => 'Test message 5',
            'rejectionReason' => 'Bad request'
        ],
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::VERIFICATION_REQUESTS as $k => $data) {
            $k++;

            $referenceUser = sprintf('%s_%d', UserFixtures::USER_REFERENCE, $k);
            $referenceMedia = sprintf('%s_%d', MediaObjectFixtures::MEDIA_REFERENCE, $k);
            /** @var User $user */
            $user = $this->getReference($referenceUser);
            /** @var MediaObject $mediaObject */
            $mediaObject = $this->getReference($referenceMedia);

            $post = new VerificationRequest();
            $post->setOwner($user);
            $post->setImage($mediaObject);
            $post->setStatus($data['status']);
            $post->setMessage($data['message']);
            $post->setRejectionReason($data['rejectionReason']);

            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 4;
    }
}
