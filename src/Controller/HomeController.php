<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use App\Form\SearchBarType;
use App\Repository\MemberRepository;
use App\Repository\SplitterRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        SplitterRepository $splitterRepository,
        MemberRepository $memberRepository,
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
            6
        );

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'splitters' => $splitters
        ]);
    }
}
