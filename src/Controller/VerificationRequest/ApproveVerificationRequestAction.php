<?php declare(strict_types=1);

namespace App\Controller\VerificationRequest;

use App\Entity\VerificationRequest;
use App\Service\VerificationRequestService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

final class ApproveVerificationRequestAction
{
    /**
     * @var VerificationRequestService
     */
    private $verificationRequestService;

    /**
     * ApproveVerificationRequestAction constructor.
     *
     * @param VerificationRequestService $verificationRequestService
     */
    public function __construct(VerificationRequestService $verificationRequestService)
    {
        $this->verificationRequestService = $verificationRequestService;
    }

    /**
     * @param VerificationRequest $data
     *
     * @return VerificationRequest
     */
    public function __invoke(VerificationRequest $data): VerificationRequest
    {
        try {
            $this->verificationRequestService->approve($data);
        } catch (Throwable $exception) {
            throw new BadRequestHttpException('Approve process is failed');
        }

        return $data;
    }
}