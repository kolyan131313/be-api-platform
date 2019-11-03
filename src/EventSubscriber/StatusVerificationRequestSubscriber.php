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
                ['onPostWrite', EventPriorities::POST_WRITE]
            ],
        ];
    }

    public function onPostWrite(ViewEvent $event): void
    {
        $object = $event->getControllerResult();
        $request = $event->getRequest();

        if (!$object instanceof VerificationRequest || !$request->isMethod(Request::METHOD_PUT)) {
            return;
        }

        if (VerificationStatusEnum::isFinishedStatus($object->getStatus())) {
            $this->notificationManager->notifyOnRequestStatusFinalized($object);
        }
    }
}