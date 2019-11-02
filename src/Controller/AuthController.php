<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * AuthController Constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register new user
     *
     * @Route("/api/register", name="register", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function register(Request $request)
    {
        $newUserData['email'] = $request->get('email');
        $newUserData['password'] = $request->get('password');
        $newUserData['firstName'] = $request->get('firstName');
        $newUserData['lastName'] = $request->get('lastName');

        $user = $this->userRepository->createNewUser($newUserData);

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }
}
