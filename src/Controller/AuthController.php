<?php declare(strict_types=1);

namespace App\Controller;

use Throwable;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

class AuthController extends AbstractController
{
    /**
     * @var UserRepository $userService
     */
    private $userService;

    /**
     * @var UserFactory $userFactory
     */
    private $userFactory;

    /**
     * @var ValidatorInterface $userService
     */
    private $validator;

    /**
     * AuthController Constructor
     *
     * @param UserService $userService
     * @param ValidatorInterface $validator
     * @param UserFactory $userFactory
     */
    public function __construct(UserService $userService, ValidatorInterface $validator, UserFactory $userFactory)
    {
        $this->userService = $userService;
        $this->userFactory = $userFactory;
        $this->validator = $validator;
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
            $userData = (array)json_decode($request->getContent());

            /** @var User $user */
            $user = $this->userFactory->make($userData);
            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                throw new ValidationException($errors);
            }

            /** @var User $user */
            $user = $this->userService->createUser($user);
        } catch (ValidationException $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage(), $exception);
        } catch (Throwable $exception) {
            throw new BadRequestHttpException('Invalid registration request data');
        }

        return new JsonResponse(
            ['code' => JsonResponse::HTTP_CREATED, 'email' => $user->getUsername()],
            JsonResponse::HTTP_CREATED,
            ['Content-type' => 'application/json']
        );
    }
}
