<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRolesEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public const USER_REFERENCE = 'user';
    public const DEFAULT_PASSWORD = 'secret12345';
    public const SIMPLE_USER_EMAIL = 'simple_user@test.com';
    public const SIMPLE_USER_EMAIL2 = 'simple_user2@test.com';
    public const SIMPLE_USER_EMAIL3 = 'simple_user3@test.com';
    public const BLOGGER_USER_EMAIL = 'blogger_user@test.com';
    public const ADMIN_USER_EMAIL = 'admin_user@test.com';

    private const USERS = [
        [
            'email' => self::SIMPLE_USER_EMAIL,
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => self::SIMPLE_USER_EMAIL2,
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => self::SIMPLE_USER_EMAIL3,
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => 'simple_user4@test.com',
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => 'simple_user5@test.com',
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::SIMPLE_USER],
            'firstName' => 'Simple',
            'lastName' => 'User',
        ],
        [
            'email' => self::BLOGGER_USER_EMAIL,
            'password' => self::DEFAULT_PASSWORD,
            'roles' => [UserRolesEnum::BLOGGER],
            'firstName' => 'Blogger',
            'lastName' => 'User',
        ],
        [
            'email' => self::ADMIN_USER_EMAIL,
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

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $k => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstName($userData['firstName']);
            $user->setLastName($userData['lastName']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));
            $user->setRoles($userData['roles']);

            $reference = sprintf('%s_%d', self::USER_REFERENCE, $k);
            $this->addReference($reference, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}
