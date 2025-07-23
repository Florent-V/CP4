<?php

namespace App\Interface;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface pour les entités qui peuvent être partagées via des codes
 */
interface ShareableEntityInterface
{
    /**
     * Retourne l'ID de l'entité
     */
    public function getId(): mixed;

    /**
     * Retourne le nom d'affichage de l'entité pour les messages
     */
    public function getDisplayName(): string;

    /**
     * Retourne le type de l'entité partagée
     */
    public function getShareableType(): string;

    /**
     * Vérifie si l'utilisateur peut accéder à cette entité partagée
     * @param UserInterface $user L'utilisateur qui souhaite accéder (peut être null pour invités).
     */
    public function canBeAccessedBy(UserInterface $user): bool;

    /**
     * Vérifie si l'utilisateur peut partager cette entité
     * @param UserInterface $user L'utilisateur qui souhaite partager
     */
    public function canBeSharedBy(UserInterface $user): bool;

    /**
     * Définit les permissions d'accès après utilisation du code de partage
     */
    public function grantAccessTo(UserInterface $user): void;
}
