<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Service\BalanceCalculator;
use App\Service\SplitterAccessManager;
use App\Service\SplitterExpenseAggregator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}',
    name: 'app_splitter_show',
    requirements: [
        'id' => '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-6[0-9a-fA-F]{3}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$'
    ],
    methods: ['GET']
)]
class ShowController extends AbstractController
{
    public function __invoke(
        Splitter $splitter,
        BalanceCalculator $balanceCalculator,
        SplitterAccessManager $accessManager,
        SplitterExpenseAggregator $expenseAggregator
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        $balancePerId = $balanceCalculator->calculateIndividualBalance($splitter);
        $transfers = $balanceCalculator->calculateTransfer($balancePerId);

        $aggregated = $expenseAggregator->aggregateExpenses($splitter);

        return $this->render('splitter/show.html.twig', [
            'user' => $user,
            'splitter' => $splitter,
            'groupTotal' => $aggregated['groupTotal'],
            'expensesByDate' => $aggregated['expensesByDate'],
            'balancePerId' => $balancePerId,
            'transfers' => $transfers,
            'memberTotals' => $aggregated['memberTotals'],
        ]);
    }
}
