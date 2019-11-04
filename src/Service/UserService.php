<?php declare(strict_types=1);

namespace App\Service;

use App\Factory\UserFactory;
use App\Entity\User;
use App\Repository\UserRepository;
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
     * UserRepository $userRepository
     */
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
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
        return (array)json_decode($request->getContent());
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

        return $this->userRepository->save($user);
    }

    /**
     * Check if user exist
     *
     * @param array $userData
     *
     * @return User
     */
    public function existUser(array $userData): ?User
    {
        $email = $userData['email'] ?? '';

        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
