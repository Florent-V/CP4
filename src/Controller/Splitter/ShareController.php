<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SplitterAccessManager;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/share',
    name: 'app_splitter_share',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}'
    ],
    methods: ['GET', 'POST']
)]
class ShareController extends AbstractController
{
    public function __invoke(
        Request $request,
        Splitter $splitter,
        MailerInterface $mailer,
        SplitterAccessManager $accessManager
    ): Response {
        /**
         * @var ?User $user
         */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        return $this->render('splitter/share.html.twig', [
            'splitter' => $splitter,
        ]);
    }
}
