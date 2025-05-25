<?php

namespace App\Service;

use App\Entity\Splitter;

class SplitterExpenseAggregator
{
    /**
     * Agrège les dépenses d'un splitter :
     * - total général
     * - regroupement par date
     * - total par membre payeur
     *
     * @param Splitter $splitter
     * @return array [groupTotal, expensesByDate, memberTotals]
     */
    public function aggregateExpenses(Splitter $splitter): array
    {
        $groupTotal = 0;
        $expensesByDate = [];
        $memberTotals = [];

        foreach ($splitter->getExpenses() as $expense) {
            // Total général
            $groupTotal += $expense->getAmount();

            // Regroupement par date
            $date = $expense->getMadeAt()->format('Y-m-d');
            $expensesByDate[$date][] = $expense;

            // Total par membre payeur
            $member = $expense->getPaidBy();
            if ($member !== null) {
                $memberId = $member->getId();
                if (!isset($memberTotals[$memberId])) {
                    $memberTotals[$memberId] = 0;
                }
                $memberTotals[$memberId] += $expense->getAmount();
            }
        }
        // Tri des dates du plus récent au plus ancien
        krsort($expensesByDate);

        return [
            'groupTotal' => $groupTotal,
            'expensesByDate' => $expensesByDate,
            'memberTotals' => $memberTotals,
        ];
    }
}
