<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRolesEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private const DEFAULT_PASSWORD = 'secret12345';
    private const USERS = [
        [
            'email' => 'simple_user@test.com',
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => 'blogger_user@test.com',
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::BLOGGER],
            'firstName' => 'Blogger',
            'lastName' => 'User',
        ],
        [
            'email' => 'admin_user@test.com',
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::ADMIN],
            'firstName' => 'Admin',
            'lastName' => 'User',
        ],
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::USERS as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));
            $user->setRoles($userData['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
