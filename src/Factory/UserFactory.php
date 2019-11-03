<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    /**
     * @param array $data
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     *
     * @return User
     */
    public static function make(array $data, UserPasswordEncoderInterface $userPasswordEncoder): User
    {
        $user = new User();
        $encodedPassword = $userPasswordEncoder->encodePassword($user, $data['password']);
        $user->setEmail($data['email'])
            ->setPassword($encodedPassword)
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName']);

        return $user;
    }
}