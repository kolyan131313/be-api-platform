<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    private const POSTS = [
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

    public function load(ObjectManager $manager): void
    {
        foreach (self::POSTS as $postData) {
            /** @var User $user */
            $user = $this->getReference(User::class);
            $post = new Post();
            $post->setOwner($user);
            $post->setTitle($postData['title']);
            $post->setContent($postData['context']);
            $manager->persist($post);
            $manager->flush();
        }
    }
}
