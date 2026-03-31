<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\SplitterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/join',
    name: 'app_splitter_join',
    requirements: [
        'id' => Requirement::UUID
    ],
    methods: ['GET']
)]
class JoinController extends AbstractController
{
    public function __invoke(
        Splitter $splitter,
        SplitterRepository $splitterRepository,
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $user->getAppUser()->addFavoriteSplitter($splitter);

        $splitterRepository->save($splitter, true);

        $this->addFlash('success', '🙂 Vous avez bien rejoint le Splitter !');

        return $this->redirectToRoute(
            'app_splitter_show',
            [
                'id' => $splitter->getId()
            ],
            Response::HTTP_SEE_OTHER
        );
    }
}
