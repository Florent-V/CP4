<?php

namespace App\Controller\Splitter;

use App\Entity\Splitter;
use App\Entity\User;
use App\Entity\AppUserMember;
use App\Enum\Role;
use App\Form\MemberSelectionFormType;
use App\Repository\AppUserMemberRepository;
use App\Service\SplitterAccessManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{id}/affect',
    name: 'app_splitter_affect',
    requirements: [
        'id' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}'
    ],
    methods: ['GET', 'POST']
)]
class AffectController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(
        Splitter $splitter,
        Request $request,
        AppUserMemberRepository $appUserMemberRepo,
        EntityManagerInterface $entityManager,
        SplitterAccessManager $accessManager
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        $accessManager->checkReadAccess($splitter, $user);

        $appUser = $user->getAppUser();
        $currentSelection = $appUserMemberRepo->findOneByUserAndSplitter($appUser, $splitter);
        $form = $this->createForm(MemberSelectionFormType::class, null, [
            'members' => $splitter->getMembers()->toArray(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $member = $form->get('member')->getData();
            // Supprimer l'éventuelle entrée existante
            if ($currentSelection) {
                $entityManager->remove($currentSelection);
                $entityManager->flush();
            }

            $selection = new AppUserMember();
            $selection->setAppUser($appUser)->setSplitter($splitter)->setMember($member);

            $entityManager->persist($selection);
            $entityManager->flush();

            $this->addFlash('success', 'Votre identité de membre a été enregistrée !');
            return $this->redirectToRoute(
                'app_splitter_show',
                ['id' => $splitter->getId()],
                Response::HTTP_SEE_OTHER
            );
        }
        return $this->render('splitter/affect.html.twig', [
            'splitter' => $splitter,
            'memberSelectionForm' => $form->createView(),
            'selectedMember' => $currentSelection?->getMember(),
        ]);
    }
}
