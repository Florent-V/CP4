<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\SearchBarType;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response {

        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $queryUsers = $userRepository->findUser($data['search']);
        } else {
            $queryUsers = $userRepository->findUser();
        }

        $users = $paginator->paginate(
            $queryUsers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('admin_app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $user->setEmail('deleteduser@mail.fr');
            $uniqId = uniqid();
            $user->setPseudo('deleteduser' . $uniqId);
            $user->setFirstName('deleteduser' . $uniqId);
            $user->setLastName('deleteduser' . $uniqId);
            $user->setPhone('noPhoneNumber');

            $userRepository->save($user, true);
        }

        return $this->redirectToRoute('admin_app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
