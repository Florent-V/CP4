<?php

namespace App\Controller\Splitter;

use App\Entity\AppUserMember;
use App\Entity\Splitter;
use App\Entity\Member;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\AppUserMemberRepository;
use App\Service\SplitterAccessManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/splitter/{splitterId}/associate/{memberId}',
    name: 'app_splitter_associate_member',
    requirements: [
        'splitterId' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}',
        'memberId' => '\d+'
    ],
    methods: ['POST']
)]
class AssociateMemberController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(
        #[MapEntity(mapping: ['splitterId' => 'id'])] Splitter $splitter,
        #[MapEntity(mapping: ['memberId' => 'id'])] Member $member,
        AppUserMemberRepository $appUserMemberRepo,
        EntityManagerInterface $entityManager,
        SplitterAccessManager $accessManager
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $accessManager->checkReadAccess($splitter, $user);
        $appUser = $user->getAppUser();
        // Vérifie que ce membre appartient bien au splitter
        if (!$splitter->getMembers()->contains($member)) {
            throw $this->createAccessDeniedException('Ce membre ne fait pas partie de ce groupe.');
        }
        $currentSelection = $appUserMemberRepo->findOneByUserAndSplitter($appUser, $splitter);
        if ($currentSelection) {
            $entityManager->remove($currentSelection);
            $entityManager->flush();
        }

        // Crée la relation entre l'utilisateur et le membre
        $relation = new AppUserMember();
        $relation->setAppUser($appUser);
        $relation->setMember($member);
        $relation->setSplitter($splitter);

        $entityManager->persist($relation);
        $entityManager->flush();

        return $this->redirectToRoute('app_splitter_show', ['id' => $splitter->getId()]);
    }
}
