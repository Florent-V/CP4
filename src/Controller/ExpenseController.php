<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/expense')]
class ExpenseController extends AbstractController
{
    #[Route('/', name: 'app_expense_index', methods: ['GET'])]
    public function index(ExpenseRepository $expenseRepository): Response
    {
        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findAll(),
        ]);
    }

    #[Route(
        '/splitter/{id}/new',
        name: 'app_expense_new',
        requirements: [
            'id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'
        ],
        methods: ['GET', 'POST']
    )]
    public function new(
        Request $request,
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

    #[Route('/{id}', name: 'app_expense_show', methods: ['GET'])]
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    #[Route(
        '/splitter/{splitter_id}/edit/{expense_id}',
        name: 'app_expense_edit',
        requirements: [
            'splitter_id' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$',
            'expense_id' => '\d+'
        ],
        methods: ['GET', 'POST']
    )]
    #[Entity('splitter', options: ['mapping' => ['splitter_id' => 'id']])]
    #[Entity('expense', options: ['mapping' => ['expense_id' => 'id']])]
    public function edit(
        Request $request,
        Splitter $splitter,
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

    #[Route('/{id}', name: 'app_expense_delete', methods: ['POST'])]
    public function delete(Request $request, Expense $expense, ExpenseRepository $expenseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute('app_expense_index', [], Response::HTTP_SEE_OTHER);
    }
}
