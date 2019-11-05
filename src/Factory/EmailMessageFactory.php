<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use Swift_Message;
use Symfony\Component\Templating\EngineInterface;

class EmailMessageFactory
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $emailSender;

    /**
     * @var VerificationStatusEnum
     */
    private $verificationStatusEnum;

    public function __construct(
        EngineInterface $templating,
        VerificationStatusEnum $verificationStatusEnum,
        string $emailSender
    ) {
        $this->templating = $templating;
        $this->verificationStatusEnum = $verificationStatusEnum;
        $this->emailSender = $emailSender;
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
            ->setFrom($this->emailSender)
            ->setTo($verificationRequest->getOwner()->getEmail())
            ->setBody($notificationTemplate);
    }
}