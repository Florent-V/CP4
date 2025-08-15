<?php

namespace App\Scheduler\Handler;

use App\Repository\ShareCodeRepository;
use App\Scheduler\Message\CleanupExpiredShareCodesMessage;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CleanupExpiredShareCodesMessageHandler
{
    public function __construct(
        private ShareCodeRepository $shareCodeRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(CleanupExpiredShareCodesMessage $message): void
    {
        try {
            $deletedCount = $this->shareCodeRepository->deleteExpiredCodes();

            if ($deletedCount > 0) {
                $this->logger->info('Codes de partage expirés supprimés automatiquement', [
                    'count' => $deletedCount,
                    'timestamp' => new DateTime()
                ]);
            } else {
                $this->logger->debug('Aucun code expiré à supprimer lors du nettoyage automatique');
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du nettoyage automatique des codes expirés', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-lancer pour que Messenger gère les retry
        }
    }
}
