<?php

namespace App\Service;

use App\Interface\ShareableEntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Service pour la validation des entités partageables
 */
readonly class ShareEntityValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    /**
     * Récupère et valide une entité partageable
     *
     * @throws NotFoundHttpException Si l'entité n'existe pas ou le type est invalide
     * @throws AccessDeniedHttpException Si l'entité n'est pas partageable ou permissions insuffisantes
     */
    public function getAndValidateEntity(string $entityType, string $entityId): ShareableEntityInterface
    {
        // Construire le nom de classe à partir du type
        $entityClass = 'App\\Entity\\' . ucfirst($entityType);

        if (!class_exists($entityClass)) {
            throw new NotFoundHttpException('Type d\'entité non trouvé : ' . $entityType);
        }

        // Récupérer l'entité
        $repository = $this->entityManager->getRepository($entityClass);
        $entity = $repository->find($entityId);

        if (!$entity) {
            throw new NotFoundHttpException('Entité non trouvée');
        }

        // Vérifier que l'entité implémente l'interface ShareableEntityInterface
        if (!$entity instanceof ShareableEntityInterface) {
            throw new AccessDeniedHttpException('Cette entité ne peut pas être partagée');
        }

        // Vérifier les permissions d'accès
        $user = $this->security->getUser();
        if (!$entity->canBeSharedBy($user)) {
            throw new AccessDeniedHttpException('Vous n\'avez pas les permissions pour partager cette ressource');
        }

        return $entity;
    }

    /**
     * Formate les données de réponse communes
     */
    public function formatShareResponse(
        ShareableEntityInterface $entity,
        object $shareCode,
        string $type,
        array $additionalData = []
    ): array {
        $baseResponse = [
            'success' => true,
            'type' => $type,
            'expiresAt' => $shareCode->getExpiresAt()->format('d/m/Y à H:i'),
            'entityDisplayName' => $entity->getDisplayName(),
        ];

        return array_merge($baseResponse, $additionalData);
    }
}
