<?php

namespace App\Command;

use App\Service\ShareCodeManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cleanup-expired-share-codes',
    description: 'Nettoie les codes de partage expirés de la base de données'
)]
class CleanupExpiredShareCodesCommand extends Command
{
    public function __construct(private ShareCodeManager $shareCodeManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inOut = new SymfonyStyle($input, $output);

        $inOut->title('Nettoyage des codes de partage expirés');

        try {
            $deletedCount = $this->shareCodeManager->cleanupExpiredCodes();

            if ($deletedCount > 0) {
                $inOut->success(sprintf('✅ %d code(s) de partage expiré(s) supprimé(s).', $deletedCount));
            } else {
                $inOut->info('ℹ️  Aucun code de partage expiré à supprimer.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $inOut->error('❌ Erreur lors du nettoyage : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
