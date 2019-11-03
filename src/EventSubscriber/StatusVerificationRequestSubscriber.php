<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\VerificationRequest;
use App\Enum\VerificationStatusEnum;
use App\Service\NotificationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Storage\StorageInterface;

final class StatusVerificationRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    public function __construct(StorageInterface $storage, NotificationManager $notificationManager)
    {
        $this->storage = $storage;
        $this->notificationManager = $notificationManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['onPreWrite', EventPriorities::PRE_WRITE],
                ['onPostWrite', EventPriorities::POST_WRITE]
            ],
        ];
    }

    public function onPreWrite(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$object instanceof VerificationRequest || Request::METHOD_PUT !== $method) {
            return;
        }

        $isFinishedStatus = VerificationStatusEnum::isFinishedStatus($object->getStatus());

        if ($isFinishedStatus && !$object->getOwner()->isAdmin()) {
            throw new ConflictHttpException(
                sprintf(
                    'You can\'t modify Verification Request in %s status',
                    VerificationStatusEnum::getLabels()[$object->getStatus()]
                )
            );
        }
    }

    public function onPostWrite(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$object instanceof VerificationRequest || Request::METHOD_PUT !== $method) {
            return;
        }

        if (VerificationStatusEnum::isFinishedStatus($object->getStatus())) {
            $this->notificationManager->notifyOnRequestStatusFinalized($object);
        }
    }
}