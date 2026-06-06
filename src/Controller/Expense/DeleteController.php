<?php

namespace App\Controller\Expense;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\ExpenseRepository;
use App\Service\ExpenseAccessManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{splitter_id}/expense/{expense_id}',
    name: 'app_expense_delete',
    requirements: [
        'splitter_id' => Requirement::UUID,
        'expense_id' => '\d+',
    ],
    methods: ['POST']
)]
class DeleteController extends AbstractController
{
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
        ExpenseRepository $expenseRepository,
        ExpenseAccessManager $accessManager,
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        $accessManager->checkAdminAccess($splitter, $expense, $user);

        if (
            $this->isCsrfTokenValid(
                'delete' . $expense->getId(),
                $request->request->get('_token')
            )
        ) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute(
            'app_splitter_show',
            ['id' => $splitter->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
}
