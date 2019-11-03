<?php declare(strict_types=1);

namespace App\Controller\VerificationRequest\actions;

use App\Entity\VerificationRequest;
use App\Service\VerificationRequestService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

final class DeclineVerificationRequestAction
{
    /**
     * @var VerificationRequestService
     */
    private $verificationRequestService;

    public function __construct(VerificationRequestService $verificationRequestService)
    {
        $this->verificationRequestService = $verificationRequestService;
    }

    public function __invoke(VerificationRequest $data): VerificationRequest
    {
        try {
            $this->verificationRequestService->decline($data);
        } catch (Throwable $exception) {
            throw new BadRequestHttpException('Decline process is failed');
        }

        return $data;
    }
}