<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MediaObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MediaObjectFixtures extends Fixture implements OrderedFixtureInterface
{
    public const MEDIA_REFERENCE = 'media';
    private const MEDIA_OBJECTS = [
        [
            'image' => 'test_img1.pmg'
        ],
        [
            'image' => 'test_img2.pmg'
        ],
        [
            'image' => 'test_img3.pmg'
        ],
        [
            'image' => 'test_img4.pmg'
        ],
        [
            'image' => 'test_img5.pmg'
        ],
        [
            'image' => 'test_img6.pmg'
        ],
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::MEDIA_OBJECTS as $k => $objects) {
            $mediaObject = new MediaObject();
            $mediaObject->setFilePath($objects['image']);
            $manager->persist($mediaObject);

            $reference = sprintf('%s_%d', self::MEDIA_REFERENCE, $k);
            $this->addReference($reference, $mediaObject);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 2;
    }
}
