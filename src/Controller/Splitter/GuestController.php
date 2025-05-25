<?php

namespace App\Controller;

use App\Entity\Splitter;
use App\Service\BalanceCalculator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/splitter')]
class GuestController extends AbstractController
{
    #[Route(
        '/{id}/guest/{unique_id}',
        name: 'app_splitter_guest_show',
        requirements: [
            'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}',
            'unique_id' => '^[0-9a-f]{32}$'
        ],
        methods: ['GET']
    )]
    public function show(
        #[MapEntity(mapping: [
            'id' => 'id',
            'unique_id' => 'uniqueId'
        ])]
        Splitter $splitter,
        BalanceCalculator $balanceCalculator
    ): Response {

        $balancePerId = $balanceCalculator->calculateIndividualBalance($splitter);
        $transfers = $balanceCalculator->calculateTransfer($balancePerId);

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

        return $this->render('splitter/show.html.twig', [
            'splitter' => $splitter,
            'groupTotal' => $groupTotal,
            'expensesByDate' => $expensesByDate,
            'balancePerId' => $balancePerId,
            'transfers' => $transfers,
            'memberTotals' => $memberTotals,
        ]);
    }
}
