<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Entity\User;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/splitter/{splitter_id}/expense', name: 'app_expense_')]
class ExpenseController extends AbstractController
{
    /**
     * Vérifie si l'utilisateur actuel a accès à la ressource splitter.
     *
     * @param Splitter $splitter
     * @param Expense $expense
     */
    private function rejectIfNotAdmin(Splitter $splitter, Expense $expense): void
    {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        if (
            $splitter->getOwner() !== $user->getAppUser()
            && $expense->getAddedBy() !== $user->getAppUser()
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
    }

    /**
     * Vérifie si l'utilisateur actuel a accès à la ressource splitter.
     *
     * @param Splitter $splitter
     * @throws AccessDeniedException Si l'utilisateur n'a pas accès
     */
    private function rejectIfNotMember(Splitter $splitter): void
    {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        if (!$user->getAppUser()->getFavoriteSplitters()->contains($splitter) && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
    }
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

        $this->rejectIfNotMember($splitter);
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

        $this->rejectIfNotMember($splitter);

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

        $this->rejectIfNotAdmin($splitter, $expense);

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
        #[MapEntity(mapping: ['splitter_id' => 'id'])]
        Splitter $splitter,
        #[MapEntity(mapping: ['expense_id' => 'id'])]
        Expense $expense,
        ExpenseRepository $expenseRepository
    ): Response {

        $this->rejectIfNotAdmin($splitter, $expense);

        if ($this->isCsrfTokenValid('delete' . $splitter->getId(), $request->request->get('_token'))) {
            $expenseRepository->remove($expense, true);
        }

        return $this->redirectToRoute(
            'app_splitter_show',
            [
                'id' => $splitter->getId(),
            ],
            Response::HTTP_SEE_OTHER
        );
    }
}
