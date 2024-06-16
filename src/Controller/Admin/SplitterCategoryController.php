<?php

namespace App\Controller\Admin;

use App\Entity\SplitterCategory;
use App\Form\SplitterCategoryType;
use App\Repository\SplitterCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/splitter/category')]
class SplitterCategoryController extends AbstractController
{
    #[Route('/', name: 'app_splitter_category_index', methods: ['GET'])]
    public function index(SplitterCategoryRepository $categoryRepository): Response
    {
        return $this->render('/admin/splitter_category/index.html.twig', [
            'splitter_categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_splitter_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $splitterCategory = new SplitterCategory();
        $form = $this->createForm(SplitterCategoryType::class, $splitterCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($splitterCategory);
            $entityManager->flush();

            return $this->redirectToRoute('admin_app_splitter_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/splitter_category/new.html.twig', [
            'splitter_category' => $splitterCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_splitter_category_show', methods: ['GET'])]
    public function show(SplitterCategory $splitterCategory): Response
    {
        return $this->render('admin/splitter_category/show.html.twig', [
            'splitter_category' => $splitterCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_splitter_category_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        SplitterCategory $splitterCategory,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(SplitterCategoryType::class, $splitterCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_app_splitter_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/splitter_category/edit.html.twig', [
            'splitter_category' => $splitterCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_splitter_category_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        SplitterCategory $splitterCategory,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $splitterCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($splitterCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_app_splitter_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
