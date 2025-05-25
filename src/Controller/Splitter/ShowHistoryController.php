<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\LogEntryRepository;
use App\Entity\Expense;
use App\Service\SplitterAccessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/history',
    name: 'app_splitter_show_history',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}'
    ],
    methods: ['GET']
)]
class ShowHistoryController extends AbstractController
{
    public function __invoke(
        Splitter $splitter,
        LogEntryRepository $logEntryRepository,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        // Récupérer les logs du Splitter
        $splitterLogs = $logEntryRepository->findLogsWithUserInfoBySplitterId($splitter->getId());

        // Récupérer les logs des Expenses associées
        $expenseIds = $splitter->getExpenses()->map(fn($expense) => $expense->getId())->toArray();
        $expenseLogs = $logEntryRepository->findLogsForMultipleEntities(Expense::class, $expenseIds);

        return $this->render('splitter/history.html.twig', [
            'splitter' => $splitter,
            'splitterLogs' => $splitterLogs,
            'expenseLogs' => $expenseLogs
        ]);
    }
}
