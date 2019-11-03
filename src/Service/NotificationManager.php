<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Templating\EngineInterface;

class NotificationManager
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct(Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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
        $notificationTemplate = $this->templating->render(
            'email/verified-request-processed.html.twig',
            [
                'verificationRequest' => $verificationRequest,
                'status' => VerificationStatusEnum::getLabels()[$verificationRequest->getStatus()]
            ]
        );

        $message = (new Swift_Message('Status update just happened!'))
            ->setFrom('admin@example.com')
            ->setTo($verificationRequest->getOwner()->getEmail())
            ->setBody($notificationTemplate);

        $this->mailer->send($message);
    }
}
