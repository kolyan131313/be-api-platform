<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public const POSTS = [
        [
            'title' => 'First title',
            'content' => 'First content'
        ],
        [
            'title' => 'Second title',
            'content' => 'Second content'
        ],
        [
            'title' => 'Third title',
            'content' => 'Third content'
        ],
    ];

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::POSTS as $k => $postData) {
            $reference = sprintf('%s_%d', UserFixtures::USER_REFERENCE, $k);
            /** @var User $user */
            $user = $this->getReference($reference);
            $post = new Post();
            $post->setOwner($user);
            $post->setTitle($postData['title']);
            $post->setContent($postData['content']);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
