<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\Splitter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class ExpenseAccessManager
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function checkMemberAccess(Splitter $splitter, ?User $user): User
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

        return $user;
    }

    public function checkAdminAccess(Splitter $splitter, Expense $expense, ?User $user): void
    {
        if ($user === null) {
            throw new AccessDeniedException('Utilisateur non connecté.');
        }
        if (
            $splitter->getOwner() !== $user->getAppUser()
            && $expense->getAddedBy() !== $user->getAppUser()
            && !$this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            throw new AccessDeniedException('Accès non autorisé à cette ressource.');
        }
    }
}
