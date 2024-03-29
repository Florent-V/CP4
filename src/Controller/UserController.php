<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/account')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_account', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
    ): Response {

        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        return $this->render('user/show.html.twig', [
            'user' => $userRepository->findOneBy([
                'id' => $user->getId(),
            ]),
        ]);
    }

    #[Route('/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        UserRepository $userRepository
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($user !== $this->getUser()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can delete himself !');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $user->setEmail('deleteduser@mail.fr');
            $uniqId = uniqid();
            $user->setPseudo('deleteduser' . $uniqId);
            $user->setFirstName('deleteduser' . $uniqId);
            $user->setLastName('deleteduser' . $uniqId);
            $user->setPhone('deleteduser' . $uniqId);

            $userRepository->save($user, true);
        }

        return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
    }
}
