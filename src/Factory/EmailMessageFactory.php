<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use Swift_Message;
use Symfony\Component\Templating\EngineInterface;

class EmailMessageFactory
{
    private const EMAIL_SENDER = 'admin@example.com';

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var VerificationStatusEnum
     */
    private $verificationStatusEnum;

    public function __construct(EngineInterface $templating, VerificationStatusEnum $verificationStatusEnum)
    {
        $this->templating = $templating;
        $this->verificationStatusEnum = $verificationStatusEnum;
    }

    /**
     * @param VerificationRequest $verificationRequest
     *
     * @param string $subject
     *
     * @return Swift_Message
     */
    public function makeFinalizeMessage(VerificationRequest $verificationRequest, string $subject): Swift_Message
    {
        $label = $this->verificationStatusEnum->getLabels()[$verificationRequest->getStatus()];

        $notificationTemplate = $this->templating->render(
            'email/verified-request-processed.html.twig',
            ['verificationRequest' => $verificationRequest, 'status' => $label]
        );

        return (new Swift_Message($subject))
            ->setFrom(self::EMAIL_SENDER)
            ->setTo($verificationRequest->getOwner()->getEmail())
            ->setBody($notificationTemplate);
    }
}