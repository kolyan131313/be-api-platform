<?php declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use App\Service\NotificationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class StatusVerificationRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * @var VerificationStatusEnum
     */
    private $verificationStatusEnum;

    public function __construct(
        NotificationManager $notificationManager,
        VerificationStatusEnum $verificationStatusEnum
    )
    {
        $this->notificationManager = $notificationManager;
        $this->verificationStatusEnum = $verificationStatusEnum;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['onPostWrite', EventPriorities::POST_WRITE]
            ],
        ];
    }

    /**
     * Send email after finalization
     *
     * @param ViewEvent $event
     */
    public function onPostWrite(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $request = $event->getRequest();

        if (!$object instanceof VerificationRequest || !$request->isMethod(Request::METHOD_PUT)) {
            return;
        }

        if ($this->verificationStatusEnum->isFinishedStatus($object->getStatus())) {
            $this->notificationManager->notifyOnRequestStatusFinalized($object);
        }
    }
}