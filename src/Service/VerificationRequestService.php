<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\VerificationRequest;
use App\Enum\UserRolesEnum;
use App\Entity\User;
use App\Enum\VerificationStatusEnum;
use App\Repository\UserRepository;
use App\Repository\VerificationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class VerificationRequestService
{
    /**
     * UserRepository $userRepository
     */
    private $userRepository;

    /**
     * VerificationRequestRepository $verificationRequestRepository
     */
    private $verificationRequestRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        UserRepository $userRepository,
        VerificationRequestRepository $verificationRequestRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->userRepository = $userRepository;
        $this->verificationRequestRepository = $verificationRequestRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Approve verification request for user
     *
     * @param VerificationRequest $verificationRequest
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function approve(VerificationRequest $verificationRequest): void
    {
        /** @var User $user */
        $user = $verificationRequest->getOwner();

        if (!in_array(UserRolesEnum::ADMIN, $user->getRoles())) {
            $user->setRoles([UserRolesEnum::BLOGGER]);
        }

        $verificationRequest->setStatus(VerificationStatusEnum::APPROVED);
        $this->entityManager->flush();
    }

    /**
     * Decline verification request for user
     *
     * @param VerificationRequest $verificationRequest
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function decline(VerificationRequest $verificationRequest): void
    {
        $verificationRequest->setStatus(VerificationStatusEnum::DECLINED);
        $this->entityManager->flush();
    }
}
