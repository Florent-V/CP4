<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Form\SplitterFormType;
use App\Repository\SplitterRepository;
use App\Service\SplitterAccessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/edit',
    name: 'app_splitter_edit',
    requirements: [
        'id' => Requirement::UUID
    ],
    methods: ['GET', 'POST']
)]
class EditController extends AbstractController
{
    public function __invoke(
        Request $request,
        Splitter $splitter,
        SplitterRepository $splitterRepository,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkEditAccess($splitter, $user);

        $form = $this->createForm(SplitterFormType::class, $splitter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $splitterRepository->save($splitter, true);

            $this->addFlash('success', '🙂 Votre Splitter a bien été édité !');

            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }
        return $this->render('splitter/edit.html.twig', [
            'form' => $form,
            'splitter' => $splitter
        ]);
    }
}
