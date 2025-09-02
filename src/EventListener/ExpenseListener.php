<?php

namespace App\EventListener;

use App\Entity\Expense;
use App\Service\ExpenseNotifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Expense::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Expense::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Expense::class)]
readonly class ExpenseListener
{
    public function __construct(private ExpenseNotifier $notifier)
    {
    }

    public function postPersist(Expense $expense): void
    {
        $this->notifier->sendNotification($expense, 'created');
    }

    public function postUpdate(Expense $expense): void
    {
        $this->notifier->sendNotification($expense, 'updated');
    }

    public function postRemove(Expense $expense): void
    {
        $this->notifier->sendNotification($expense, 'deleted');
    }
}
