<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class AuthController extends AbstractController
{
    /**
     * @var UserRepository $userService
     */
    private $userService;

    /**
     * AuthController Constructor
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register new user
     *
     * @Route("/api/register", name="api_register", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->prepareUserData($request);
            /** @var User $user */
            $user = $this->userService->createUser($data);
        } catch (Throwable $exception) {
            throw new BadRequestHttpException('Invalid registration request data');
        }

        return new JsonResponse([
            'code' => JsonResponse::HTTP_CREATED,
            'email' => $user->getUsername()
        ], JsonResponse::HTTP_CREATED);
    }
}
