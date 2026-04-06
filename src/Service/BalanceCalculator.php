<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Enum\SplitType;
use App\Repository\MemberRepository;

readonly class BalanceCalculator
{
    public function __construct(
        private MemberRepository $memberRepository
    ) {
    }

    public function calculateIndividualBalance(Splitter $splitter): array
    {
        $balancePerId = [];
        //Initialize amount to 0 for all members
        foreach ($splitter->getMembers() as $member) {
            $balancePerId[$member->getId()] = 0;
        }

        //Calculate total amount by member
        foreach ($splitter->getExpenses() as $expense) {
            $amount = $expense->getAmount();
            $payer = $expense->getPaidBy()->getId();
            $balancePerId[$payer] += $amount;

            match ($expense->getSplitType()) {
                SplitType::EQUAL => $this->applyEqualSplit($expense, $balancePerId),
                SplitType::PERCENTAGE => $this->applyPercentageSplit($expense, $balancePerId),
                SplitType::AMOUNT => $this->applyAmountSplit($expense, $balancePerId),
            };
        }

        //Apply transfers: from gives money to to
        foreach ($splitter->getTransfers() as $transfer) {
            $amount = $transfer->getAmount();
            $fromId = $transfer->getFromMember()->getId();
            $toId = $transfer->getToMember()->getId();

            // The person giving money increases their balance (they've paid their debt)
            $balancePerId[$fromId] += $amount;
            // The person receiving money decreases their balance (they've received what they were owed)
            $balancePerId[$toId] -= $amount;
        }

        asort($balancePerId);

        return $balancePerId;
    }

    private function applyEqualSplit(Expense $expense, array &$balancePerId): void
    {
        $beneficiaries = $expense->getBeneficiaries();
        $share = $expense->getAmount() / count($beneficiaries);
        foreach ($beneficiaries as $beneficiary) {
            $balancePerId[$beneficiary->getId()] -= $share;
        }
    }

    private function applyPercentageSplit(Expense $expense, array &$balancePerId): void
    {
        foreach ($expense->getShares() as $expenseShare) {
            $share = $expense->getAmount() * ($expenseShare->getShare() / 100);
            $balancePerId[$expenseShare->getMember()->getId()] -= $share;
        }
    }

    private function applyAmountSplit(Expense $expense, array &$balancePerId): void
    {
        foreach ($expense->getShares() as $expenseShare) {
            $balancePerId[$expenseShare->getMember()->getId()] -= $expenseShare->getShare();
        }
    }

    public function calculateTransfer(array $balancePerId): array
    {
        //separate keys (id) and values (amount) from balance array
        $sortedMembers = array_keys($balancePerId);
        $sortedBalance = array_values($balancePerId);

        //initialize index of minimum and maximum amount
        $min = 0;
        $max = count($balancePerId) - 1;

        //Initialize transfer array
        $transfers = [];

        while ($min < $max) {
            $delta = min(-$sortedBalance[$min], $sortedBalance[$max]);
            $sortedBalance[$min] += $delta;
            $sortedBalance[$max] -= $delta;

            // On utilise >= 0.01 pour inclure exactement 1 centime
            if ($delta >= 0.01) {
                $transfers[] = [
                    'from' => $this->memberRepository->findOneBy([
                        'id' => $sortedMembers[$min],
                    ]),
                    'to' => $this->memberRepository->findOneBy([
                        'id' => $sortedMembers[$max],
                    ]),
                    'amount' => $delta,
                ];
            }

            // Utilisation de abs() car les floats ne sont jamais exactement == 0
            // À cause des erreurs d'arrondi (ex: -2.220446049250313E-16)
            if (abs($sortedBalance[$min]) < 0.01) {
                $min++;
            }
            if (abs($sortedBalance[$max]) < 0.01) {
                $max--;
            }
        }
        return $transfers;
    }
}
