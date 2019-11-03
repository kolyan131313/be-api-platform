<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\VerificationRequest;
use App\Enum\UserRolesEnum;
use App\Entity\User;
use App\Enum\VerificationStatusEnum;
use App\Repository\UserRepository;
use App\Repository\VerificationRequestRepository;
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

    public function __construct(
        UserRepository $userRepository,
        VerificationRequestRepository $verificationRequestRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->verificationRequestRepository = $verificationRequestRepository;
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

        if (!$user->isAdmin()) {
            $user->setRoles([UserRolesEnum::BLOGGER]);
            $this->userRepository->save($user);
        }

        $verificationRequest->setStatus(VerificationStatusEnum::APPROVED);
        $this->verificationRequestRepository->save($verificationRequest);
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
        $this->verificationRequestRepository->save($verificationRequest);
    }
}
