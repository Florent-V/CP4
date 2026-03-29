<?php

namespace App\EventListener;

use App\Entity\Transfer;
use App\Service\TransferNotifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Transfer::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Transfer::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Transfer::class)]
readonly class TransferListener
{
    public function __construct(private TransferNotifier $notifier)
    {
    }

    public function postPersist(Transfer $transfer): void
    {
        $this->notifier->sendNotification($transfer, 'created');
    }

    public function postUpdate(Transfer $transfer): void
    {
        $this->notifier->sendNotification($transfer, 'updated');
    }

    public function postRemove(Transfer $transfer): void
    {
        $this->notifier->sendNotification($transfer, 'deleted');
    }
}
