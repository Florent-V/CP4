<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\Role;
use App\Enum\ShareCodeType;
use App\Interface\ShareableEntityInterface;
use App\Service\ShareCodeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/s/{token}',
    name: 'app_join_by_secure_link',
    requirements: [
        'token' => '[a-zA-Z0-9]{32}'  // Token de 32 caractères alphanumériques
    ],
    methods: ['GET']
)]
class JoinBySecureLinkController extends AbstractController
{
    public function __invoke(
        string $token,
        ShareCodeManager $shareCodeManager,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var ?User $user */
        $user = $this->getUser();

        return $this->processJoinRequest($token, $shareCodeManager, $entityManager, $user);
    }

    /**
     * Traite la demande de rejoindre une entité avec un lien sécurisé
     */
    private function processJoinRequest(
        string $token,
        ShareCodeManager $shareCodeManager,
        EntityManagerInterface $entityManager,
        User $user
    ): Response {
        // Validation préalable du format avec l'Enum
        if (!$shareCodeManager->validateCodeFormat($token, ShareCodeType::LINK)) {
            return $this->redirectWithError('❌ Le lien de partage n\'a pas le bon format.');
        }

        // Utiliser le token et récupérer l'entité
        $entity = $shareCodeManager->useShareCode($token, $user->getUserIdentifier());

        if (!$entity) {
            return $this->redirectWithError('❌ Ce lien de partage est invalide, expiré ou déjà utilisé.');
        }

        if (!$entity instanceof ShareableEntityInterface) {
            return $this->redirectWithError('❌ Type d\'entité non supporté pour le partage.');
        }

        if (!$entity->canBeAccessedBy($user)) {
            return $this->redirectWithError('❌ Vous n\'avez pas les permissions pour accéder à cette ressource.');
        }

        return $this->grantAccessAndRedirect($entity, $entityManager, $user);
    }

    /**
     * Redirige avec un message d'erreur
     */
    private function redirectWithError(string $message): Response
    {
        $this->addFlash('error', $message);
        return $this->redirectToRoute('app_join_with_code');
    }

    /**
     * Accorde l'accès et redirige vers l'entité
     */
    private function grantAccessAndRedirect(
        ShareableEntityInterface $entity,
        EntityManagerInterface $entityManager,
        User $user
    ): Response {
        // Accorder l'accès à l'utilisateur
        $entity->grantAccessTo($user);
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', '🎉 Vous avez rejoint avec succès : ' . $entity->getDisplayName());

        // Rediriger vers la vue de l'entité
        return $this->redirectToEntityView($entity);
    }

    /**
     * Redirige vers la vue appropriée selon le type d'entité
     */
    private function redirectToEntityView(ShareableEntityInterface $entity): Response
    {
        $entityClass = get_class($entity);
        $entityId = $entity->getId();

        return match ($entityClass) {
            'App\Entity\Splitter' => $this->redirectToRoute('app_splitter_show', [
                'id' => $entityId,
            ]),
            default => throw $this->createNotFoundException('Type d\'entité non supporté : ' . $entityClass)
        };
    }
}
