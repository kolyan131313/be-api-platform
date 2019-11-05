<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * UserRepository $userRepository
     */
    private $userRepository;

    /**
     * Enti $entityManager
     */
    private $entityManager;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Create user for registration
     *
     * @param User $user
     *
     * @return User
     */
    public function createUser(User $user): User
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
