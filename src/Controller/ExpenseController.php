<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;

#[Route('/splitter/{splitter_id}/expense', name: 'app_expense_')]
class ExpenseController extends AbstractController
{
    #[Route(
        '/new',
        name: 'new',
        requirements: [
            'splitter_id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        ExpenseRepository $expenseRepository
    ): Response {

        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense, [
            'splitter' => $splitter
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setSplitter($splitter);
            $expense->setCreatedAt(new DateTime('now'));
            $expense->setDevise('€');
            $expenseRepository->save($expense, true);

            return $this->redirectToRoute('app_splitter_show', ['id' => $splitter->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form,
            'splitter' => $splitter,
        ]);
    }

    #[Route(
        '/{expense_id}',
        name: 'show',
        requirements: [
            'splitter_id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'expense_id' => '\d+'
        ],
        methods: ['GET']
    )]
    public function show(
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
    ): Response {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
            'splitter' => $splitter,
        ]);
    }

    #[Route(
        '/{expense_id}/edit',
        name: 'edit',
        requirements: [
            'splitter_id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'expense_id' => '\d+'
        ],
        methods: ['GET', 'POST']
    )]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
        ExpenseRepository $expenseRepository
    ): Response {
        $form = $this->createForm(ExpenseType::class, $expense, [
            'splitter' => $splitter
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseRepository->save($expense, true);

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
            'splitter' => $splitter,
        ]);
    }

    #[Route('/{expense_id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
        ExpenseRepository $expenseRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
