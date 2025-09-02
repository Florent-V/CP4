<?php

namespace App\Service;

use App\Entity\Expense;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class ExpenseNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {
    }

    public function sendNotification(Expense $expense, string $action): void
    {
        $splitter = $expense->getSplitter();
        if (!$splitter) {
            return;
        }

        $recipients = $this->userRepository->findEmailsBySplitter($splitter->getId());

        if (empty($recipients)) {
            return;
        }

        $subject = match ($action) {
            'created' => 'Nouvelle dépense ajoutée au groupe ' . $splitter->getName(),
            'updated' => 'Dépense mise à jour dans le groupe ' . $splitter->getName(),
            'deleted' => 'Dépense supprimée du groupe ' . $splitter->getName(),
            default => 'Notification de dépense',
        };

        $email = (new TemplatedEmail())
            ->from(new Address('kopeck@f5t.fr', 'Kopeck'))
            ->to(...$recipients)
            ->subject($subject)
            ->htmlTemplate('emails/expense_notification.html.twig')
            ->context([
                'expense' => $expense,
                'splitter' => $splitter,
                'action' => $action,
            ]);

        $this->mailer->send($email);
    }
}
