<?php

namespace App\Service;

use App\Entity\Splitter;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SplitterAccessManager
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Vérifie si l'utilisateur actuel a accès en édition à la ressource splitter.
     *
     * @param Splitter $splitter
     * @param User|null $user
     * @throws AccessDeniedException
     */
    public function checkEditAccess(Splitter $splitter, ?User $user): void
    {
        if ($user === null) {
            throw new AccessDeniedException('Utilisateur non connecté.');
        }
        if (
            $splitter->getOwner() !== $user->getAppUser()
            && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource. !');
        }
    }

    /**
     * Vérifie si l'utilisateur actuel a accès en lecture à la ressource splitter.
     *
     * @param Splitter $splitter
     * @param User|null $user
     * @throws AccessDeniedException
     */
    public function checkReadAccess(Splitter $splitter, ?User $user): void
    {
        if ($user === null) {
            throw new AccessDeniedException('Utilisateur non connecté.');
        }
        if (
            !$user->getAppUser()->getFavoriteSplitters()->contains($splitter)
            && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
    }
}
