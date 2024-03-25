<?php

namespace App\Service;

use App\Entity\Splitter;
use App\Repository\MemberRepository;
use App\Repository\SplitterRepository;
use App\Repository\UserRepository;

class BalanceCalculator
{
    public function __construct(
        private readonly MemberRepository $memberRepository
    ) {
    }

    public function calculateIndividualBalance(Splitter $splitter): array
    {
        $balancePerId = [];
        $total = 0;
        //Initialize amount to 0 for all members
        foreach ($splitter->getMembers() as $member) {
            $balancePerId[$member->getId()] = 0;
        }

        //Calculate total amount by member
        foreach ($splitter->getExpenses() as $expense) {
            $amount = $expense->getAmount();
            $payer = $expense->getPaidBy()->getId();
            $balancePerId[$payer] += $amount;
            $beneficiaries = $expense->getBeneficiaries();
            $average = $amount / count($beneficiaries);
            $memberIds = array_map(fn ($member) => $member->getId(), $beneficiaries->toArray());
            foreach ($memberIds as $id) {
                $balancePerId[$id] -= $average;
            }
        }

        //Caculate balance
        $average = ($total / count($balancePerId));
        foreach ($balancePerId as $id => $amount) {
            $balancePerId[$id] = $amount - $average;
        }
        asort($balancePerId);

        return $balancePerId;
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

            $transfers[] = [
                'from' => $this->memberRepository->findOneBy([
                    'id' => $sortedMembers[$min],
                ]),
                'to' => $this->memberRepository->findOneBy([
                    'id' => $sortedMembers[$max],
                ]),
                'amount' => $delta,
            ];

            if ($sortedBalance[$min] == 0) {
                $min++;
            }
            if ($sortedBalance[$max] == 0) {
                $max--;
            }
        }
        return $transfers;
    }
}
