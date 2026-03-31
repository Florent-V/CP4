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
use App\Service\SplitterAccessManager;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/leave',
    name: 'app_splitter_leave',
    requirements: [
        'id' => Requirement::UUID
    ],
    methods: ['GET']
)]
class LeaveController extends AbstractController
{
    public function __invoke(
        Splitter $splitter,
        SplitterRepository $splitterRepository,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        $user->getAppUser()->removeFavoriteSplitter($splitter);
        $splitterRepository->save($splitter, true);

        $this->addFlash('success', '🙂 Vous avez bien quitté le Splitter !');

        return $this->redirectToRoute(
            'app_home',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
