<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Repository\ExpenseRepository;
use App\Repository\LogEntryRepository;

class HistoryService
{
    public function __construct(
        private readonly LogEntryRepository $logEntryRepository,
        private readonly ExpenseRepository $expenseRepository
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

        // 3. Récupérer les dépenses supprimées (soft-deleted) et les formater comme des logs
        $softDeletedExpenses = $this->expenseRepository->findSoftDeletedInSplitter($splitter->getId());

        $deletedExpenseLogs = [];
        foreach ($softDeletedExpenses as $deletedExpense) {
            $deletedExpenseLogs[] = [
                'action' => 'remove',
                'loggedAt' => $deletedExpense['deletedAt'],
                'expenseName' => $deletedExpense['name'],
                'firstName' => 'Action système', // Pas d'info sur qui a supprimé
                'userEmail' => null,
                'data' => [
                    'montant' => $deletedExpense['amount'] . ' ' . $deletedExpense['devise'],
                ],
            ];
        }

        // 4. Fusionner toutes les sources de logs
        $allLogs = array_merge($splitterLogs, $expenseLogs, $deletedExpenseLogs);

        // 5. Trier le tableau final par date, du plus récent au plus ancien
        usort($allLogs, fn ($first, $second) => $second['loggedAt'] <=> $first['loggedAt']);

        return $allLogs;
    }
}
