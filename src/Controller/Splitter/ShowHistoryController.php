<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use App\Service\HistoryService;
use App\Service\SplitterAccessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/history',
    name: 'app_splitter_show_history',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}'
    ],
    methods: ['GET']
)]
class ShowHistoryController extends AbstractController
{
    public function __invoke(
        Splitter $splitter,
        HistoryService $historyService,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        $historyItems = $historyService->getCombinedHistoryForSplitter($splitter);

        return $this->render('splitter/history.html.twig', [
            'splitter' => $splitter,
            'historyItems' => $historyItems,
        ]);
    }
}
