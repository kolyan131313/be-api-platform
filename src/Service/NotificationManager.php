<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\VerificationRequest;
use App\Factory\EmailMessageFactory;
use Swift_Mailer;

class NotificationManager
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var EmailMessageFactory
     */
    private $emailMessageFactory;

    public function __construct(Swift_Mailer $mailer, EmailMessageFactory $emailMessageFactory)
    {
        $this->mailer = $mailer;
        $this->emailMessageFactory = $emailMessageFactory;
    }

    /**
     * Send notification when status of Verification request was changed
     *
     * @param VerificationRequest $verificationRequest
     *
     * @return void
     */
    public function notifyOnRequestStatusFinalized(VerificationRequest $verificationRequest): void
    {
        $message = $this->emailMessageFactory->makeFinalizeMessage($verificationRequest, 'Status update just happened!');

        $this->mailer->send($message);
    }
}
