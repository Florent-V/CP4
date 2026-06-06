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
        $expensesWithPicture = [];

        foreach ($splitter->getExpenses() as $expense) {
            $groupTotal += $expense->getAmount();

            $date = $expense->getMadeAt()->format('Y-m-d');
            $expensesByDate[$date][] = $expense;

            $member = $expense->getPaidBy();
            if ($member !== null) {
                $memberId = $member->getId();
                if (!isset($memberTotals[$memberId])) {
                    $memberTotals[$memberId] = 0;
                }
                $memberTotals[$memberId] += $expense->getAmount();
            }

            if ($expense->getPicture() !== null) {
                $expensesWithPicture[] = $expense;
            }
        }

        krsort($expensesByDate);

        return [
            'groupTotal' => $groupTotal,
            'expensesByDate' => $expensesByDate,
            'memberTotals' => $memberTotals,
            'expensesWithPicture' => $expensesWithPicture,
        ];
    }
}
