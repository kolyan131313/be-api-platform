<?php declare(strict_types=1);

namespace App\Service;

use App\Factory\UserFactory;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * EntityManager $entityManager
     */
    private $entityManager;

    /**
     * UserRepository $userRepository
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository
    )
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Prepare user data
     *
     * @param Request $request
     *
     * @return array
     */
    public function prepareUserData(Request $request): array
    {
         $userData['email'] = $request->get('email');
         $userData['password'] = $request->get('password');
         $userData['firstName'] = $request->get('firstName');
         $userData['lastName'] = $request->get('lastName');

         return $userData;
    }

    /**
     * Create user for registration
     *
     * @param array $userData
     *
     * @return User
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUser(array $userData): User
    {
        /** @var User $user */
        $user = UserFactory::make($userData, $this->encoder);

        return $this->userRepository->saveUser($user);
    }
}
