<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Form\SearchBarFormType;
use App\Repository\SplitterRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted(Role::USER->value)]
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
        $appUser = $user->getAppUser();

        $form = $this->createForm(SearchBarFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $querySplitters = $splitterRepository->findAppUserSplit($appUser, $data['search']);
        } else {
            $querySplitters = $splitterRepository->findAppUserSplit($appUser);
        }

        $splitters = $paginator->paginate(
            $querySplitters,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'form' => $form,
            'splitters' => $splitters
        ]);
    }
}
