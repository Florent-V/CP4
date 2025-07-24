<?php

namespace App\Controller\Admin;

use App\Form\SearchBarFormType;
use App\Repository\SplitterRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/splitter')]
class SplitterController extends AbstractController
{
    #[Route('/', name: 'app_splitter_index', methods: ['GET'])]
    public function index(
        Request $request,
        SplitterRepository $splitterRepository,
        PaginatorInterface $paginator
    ): Response {

        $form = $this->createForm(SearchBarFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $querySplitters = $splitterRepository->findSplitter($data['search']);
        } else {
            $querySplitters = $splitterRepository->findSplitter();
        }

        $splitters = $paginator->paginate(
            $querySplitters,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/splitter/index.html.twig', [
            'splitters' => $splitters,
            'form' => $form
        ]);
    }
}
