<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function make(array $data): User
    {
        $user = new User();
        $encodedPassword = $this->userPasswordEncoder->encodePassword($user, $data['password']);
        $user->setEmail($data['email'])
            ->setPassword($encodedPassword)
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName']);

        return $user;
    }
}