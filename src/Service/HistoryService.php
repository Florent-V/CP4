<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Entity\Transfer;
use App\Repository\ExpenseRepository;
use App\Repository\LogEntryRepository;
use App\Repository\TransferRepository;

class HistoryService
{
    public function __construct(
        private readonly LogEntryRepository $logEntryRepository,
        private readonly ExpenseRepository $expenseRepository,
        private readonly TransferRepository $transferRepository
    ) {
    }

    /**
     * Récupère, fusionne et trie l'historique complet pour un Splitter.
     */
    public function getCombinedHistoryForSplitter(Splitter $splitter): array
    {
        // 1. Récupérer les logs du Splitter lui-même
        $splitterLogs = $this->logEntryRepository->findLogsWithUserInfoBySplitterId($splitter->getId());

        // 2. Récupérer les logs des dépenses associées
        $expenseIds = $splitter->getExpenses()->map(fn (Expense $expense) => $expense->getId())->toArray();
        $expenseLogs = [];
        if (!empty($expenseIds)) {
            $expenseLogs = $this->logEntryRepository->findLogsForMultipleEntities(Expense::class, $expenseIds);
        }

        // 3. Récupérer les logs des transferts associés
        $transferIds = $splitter->getTransfers()->map(fn (Transfer $transfer) => $transfer->getId())->toArray();
        $transferLogs = [];
        if (!empty($transferIds)) {
            $transferLogs = $this->logEntryRepository->findLogsForMultipleTransfers(Transfer::class, $transferIds);
        }

        // 4. Récupérer les dépenses supprimées (soft-deleted) et les formater comme des logs
        $softDeletedExpenses = $this->expenseRepository->findSoftDeletedInSplitter($splitter->getId());

        $deletedExpenseLogs = [];
        foreach ($softDeletedExpenses as $deletedExpense) {
            $deletedExpenseLogs[] = [
                'action' => 'remove',
                'loggedAt' => $deletedExpense['deletedAt'],
                'expenseName' => $deletedExpense['name'],
                'firstName' => 'Action système',
                'userEmail' => null,
                'data' => [
                    'montant' => $deletedExpense['amount'] . ' ' . $deletedExpense['devise'],
                ],
            ];
        }

        // 5. Récupérer les transferts supprimés (soft-deleted)
        $softDeletedTransfers = $this->transferRepository->findSoftDeletedInSplitter($splitter->getId());

        $deletedTransferLogs = [];
        foreach ($softDeletedTransfers as $deletedTransfer) {
            $transferName = sprintf(
                '%s → %s',
                $deletedTransfer['fromMemberName'] ?? 'Inconnu',
                $deletedTransfer['toMemberName'] ?? 'Inconnu'
            );
            $deletedTransferLogs[] = [
                'action' => 'remove',
                'loggedAt' => $deletedTransfer['deletedAt'],
                'transferName' => $transferName,
                'firstName' => 'Action système',
                'userEmail' => null,
                'data' => [
                    'montant' => $deletedTransfer['amount'] . ' €',
                    'description' => $deletedTransfer['description'] ?? 'N/A',
                ],
            ];
        }

        // 6. Fusionner toutes les sources de logs
        $allLogs = array_merge($splitterLogs, $expenseLogs, $transferLogs, $deletedExpenseLogs, $deletedTransferLogs);

        // 7. Trier le tableau final par date, du plus récent au plus ancien
        usort($allLogs, fn ($first, $second) => $second['loggedAt'] <=> $first['loggedAt']);

        return $allLogs;
    }
}
