<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Service\BalanceCalculator;
use App\Service\SplitterExpenseAggregator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/splitter/{id}/guest/{unique_id}',
    name: 'app_splitter_guest_show',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}',
        'unique_id' => '^[0-9a-f]{32}$'
    ],
    methods: ['GET']
)]
class GuestController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: [
            'id' => 'id',
            'unique_id' => 'uniqueId'
        ])]
        Splitter $splitter,
        BalanceCalculator $balanceCalculator,
        SplitterExpenseAggregator $expenseAggregator
    ): Response {

        $balancePerId = $balanceCalculator->calculateIndividualBalance($splitter);
        $transfers = $balanceCalculator->calculateTransfer($balancePerId);

        $aggregated = $expenseAggregator->aggregateExpenses($splitter);

        return $this->render('splitter/show.html.twig', [
            'splitter' => $splitter,
            'groupTotal' => $aggregated['groupTotal'],
            'expensesByDate' => $aggregated['expensesByDate'],
            'balancePerId' => $balancePerId,
            'transfers' => $transfers,
            'memberTotals' => $aggregated['memberTotals'],
        ]);
    }
}
