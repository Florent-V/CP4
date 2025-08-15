<?php

namespace App\Service;

use App\Entity\Splitter;
use App\Entity\Member;
use App\Entity\AppUser;
use App\Entity\AppUserMember;
use App\Repository\AppUserMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SplitterMemberManager
{
    private AppUserMemberRepository $appUserMemberRepo;
    private EntityManagerInterface $entityManager;

    public function __construct(AppUserMemberRepository $appUserMemberRepo, EntityManagerInterface $entityManager)
    {
        $this->appUserMemberRepo = $appUserMemberRepo;
        $this->entityManager = $entityManager;
    }

    /**
     * Associe un membre à un utilisateur pour un splitter
     *
     * @throws AccessDeniedException|NonUniqueResultException
     */
    public function associateMemberToUser(AppUser $appUser, Splitter $splitter, Member $member): void
    {
        // Vérifie que ce membre appartient bien au splitter
        if (!$splitter->getMembers()->contains($member)) {
            throw new AccessDeniedException('Ce membre ne fait pas partie de ce groupe.');
        }
        $currentSelection = $this->appUserMemberRepo->findOneByUserAndSplitter($appUser, $splitter);
        if ($currentSelection) {
            $this->entityManager->remove($currentSelection);
            $this->entityManager->flush();
        }
        // Crée la relation entre l'utilisateur et le membre
        $relation = new AppUserMember();
        $relation->setAppUser($appUser);
        $relation->setMember($member);
        $relation->setSplitter($splitter);
        $this->entityManager->persist($relation);
        $this->entityManager->flush();
    }

    /**
     * Gère la sélection de membre (affect)
     *
     * @throws AccessDeniedException|NonUniqueResultException
     */
    public function affectMember(AppUser $appUser, Splitter $splitter, Member $member): void
    {
        $currentSelection = $this->appUserMemberRepo->findOneByUserAndSplitter($appUser, $splitter);
        if ($currentSelection) {
            $this->entityManager->remove($currentSelection);
            $this->entityManager->flush();
        }
        $selection = new AppUserMember();
        $selection->setAppUser($appUser)->setSplitter($splitter)->setMember($member);
        $this->entityManager->persist($selection);
        $this->entityManager->flush();
    }

    /**
     * Ajoute un splitter aux favoris de l'utilisateur (join)
     */
    public function joinSplitter(AppUser $appUser, Splitter $splitter): void
    {
        $appUser->addFavoriteSplitter($splitter);
        $this->entityManager->persist($appUser);
        $this->entityManager->flush();
    }

    /**
     * Retire un splitter des favoris de l'utilisateur (leave)
     */
    public function leaveSplitter(AppUser $appUser, Splitter $splitter): void
    {
        $appUser->removeFavoriteSplitter($splitter);
        $this->entityManager->persist($appUser);
        $this->entityManager->flush();
    }
}
