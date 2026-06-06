<?php

namespace App\Controller\Expense;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Service\ExpenseAccessManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{splitter_id}/expense/{expense_id}',
    name: 'app_expense_show',
    requirements: [
        'splitter_id' => Requirement::UUID,
        'expense_id' => '\d+',
    ],
    methods: ['GET']
)]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
        ExpenseAccessManager $accessManager,
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        $accessManager->checkMemberAccess($splitter, $user);

        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
            'splitter' => $splitter,
        ]);
    }
}
