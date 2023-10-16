<?php

namespace App\Controller\Admin;

use App\Entity\Splitter;
use App\Entity\User;
use App\Form\JoinSplitterType;
use App\Form\SearchBarType;
use App\Form\ShareSplitterType;
use App\Form\SplitterType;
use App\Repository\SplitterRepository;
use App\Service\BalanceCalculator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/splitter')]
class SplitterController extends AbstractController
{
    #[Route('/', name: 'app_splitter_index', methods: ['GET'])]
    public function index(
        Request $request,
        SplitterRepository $splitterRepository,
        PaginatorInterface $paginator
    ): Response {

        $form = $this->createForm(SearchBarType::class);
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

    #[Route('/{id}', name: 'app_splitter_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Splitter $splitter,
        SplitterRepository $splitterRepository
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $splitter->getId(), $request->request->get('_token'))) {
            $splitterRepository->remove($splitter, true);
        }

        return $this->redirectToRoute('admin_app_splitter_index', [], Response::HTTP_SEE_OTHER);
    }
}
