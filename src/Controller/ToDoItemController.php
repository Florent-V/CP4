<?php

namespace App\Controller;

use App\Entity\ToDoItem;
use App\Form\ToDoItemType;
use App\Repository\ToDoItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todoitem')]
class ToDoItemController extends AbstractController
{
    #[Route('/', name: 'app_to_do_item_index', methods: ['GET'])]
    public function index(ToDoItemRepository $toDoItemRepository): Response
    {
        return $this->render('to_do_item/index.html.twig', [
            'to_do_items' => $toDoItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_to_do_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $toDoItem = new ToDoItem();
        $form = $this->createForm(ToDoItemType::class, $toDoItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($toDoItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_to_do_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('to_do_item/new.html.twig', [
            'to_do_item' => $toDoItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_to_do_item_show', methods: ['GET'])]
    public function show(ToDoItem $toDoItem): Response
    {
        return $this->render('to_do_item/show.html.twig', [
            'to_do_item' => $toDoItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_to_do_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ToDoItem $toDoItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ToDoItemType::class, $toDoItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_to_do_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('to_do_item/edit.html.twig', [
            'to_do_item' => $toDoItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_to_do_item_delete', methods: ['POST'])]
    public function delete(Request $request, ToDoItem $toDoItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$toDoItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($toDoItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_to_do_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
