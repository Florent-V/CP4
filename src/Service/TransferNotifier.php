<?php

namespace App\Service;

use App\Entity\Transfer;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

readonly class TransferNotifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {
    }

    public function sendNotification(Transfer $transfer, string $action): void
    {
        $splitter = $transfer->getSplitter();
        if (!$splitter) {
            return;
        }

        $recipients = $this->userRepository->findEmailsBySplitter($splitter->getId());

        if (empty($recipients)) {
            return;
        }

        $subject = match ($action) {
            'created' => 'Nouveau transfert enregistré dans le groupe ' . $splitter->getName(),
            'updated' => 'Transfert mis à jour dans le groupe ' . $splitter->getName(),
            'deleted' => 'Transfert supprimé du groupe ' . $splitter->getName(),
            default => 'Notification de transfert',
        };

        $email = (new TemplatedEmail())
            ->from(new Address('kopeck@f5t.fr', 'Kopeck'))
            ->to(...$recipients)
            ->subject($subject)
            ->htmlTemplate('emails/transfer_notification.html.twig')
            ->context([
                'transfer' => $transfer,
                'splitter' => $splitter,
                'action' => $action,
            ]);

        $this->mailer->send($email);
    }
}
