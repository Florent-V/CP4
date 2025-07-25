<?php

// src/Schedule/CleanupScheduleProvider.php

namespace App\Scheduler;

use App\Scheduler\Message\CleanupExpiredShareCodesMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('cleanup')]
class CleanupScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every(
                    '1 hour', // Exécute toutes les heures
                    new CleanupExpiredShareCodesMessage()
                )
            )
            // Alternative : exécuter toutes les 30 minutes
            // ->add(RecurringMessage::every('30 minutes', new CleanupExpiredShareCodesMessage()))

            // Alternative : exécuter à des heures spécifiques
            // ->add(RecurringMessage::cron('0 */2 * * *', new CleanupExpiredShareCodesMessage()))
            // Toutes les 2 heures
            ;
    }
}
