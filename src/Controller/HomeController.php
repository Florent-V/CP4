<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchBarType;
use App\Repository\SplitterRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        SplitterRepository $splitterRepository,
        PaginatorInterface $paginator
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $querySplitters = $splitterRepository->findUserSplit($user, $data['search']);
        } else {
            $querySplitters = $splitterRepository->findUserSplit($user);
        }

        $splitters = $paginator->paginate(
            $querySplitters,
            $request->query->getInt('page', 1),
            9
        );

        return $this->renderForm('home/index.html.twig', [
            'form' => $form,
            'splitters' => $splitters,

        ]);
    }
}
