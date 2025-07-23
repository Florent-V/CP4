<?php

namespace App\Controller\Api;

use App\Enum\ShareCodeType;
use App\Service\ShareCodeManager;
use App\Service\ShareEntityValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;

/**
 * Contrôleur API pour la génération de liens sécurisés
 * Fonctionne uniquement en AJAX/JSON
 */
#[IsGranted(Role::USER->value)]
#[Route(
    '/api/share/link/{entityType}/{entityId}',
    name: 'api_share_link',
    requirements: ['entityType' => 'splitter|expense', 'entityId' => '.+'],
    methods: ['POST']
)]
class ShareLinkController extends AbstractController
{
    public function __invoke(
        string $entityType,
        string $entityId,
        ShareCodeManager $shareCodeManager,
        ShareEntityValidator $validator
    ): JsonResponse {
        // Valider l'entité et les permissions
        $entity = $validator->getAndValidateEntity($entityType, $entityId);

        // Générer un token sécurisé (invalide les précédents liens)
        $shareCode = $shareCodeManager->generateShareCode($entity, true, ShareCodeType::LINK);

        // Générer l'URL sécurisée
        $secureUrl = $this->generateUrl('app_join_by_secure_link', [
            'token' => $shareCode->getCode()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Formater la réponse
        $response = $validator->formatShareResponse($entity, $shareCode, 'link', [
            'shareUrl' => $secureUrl,
            'token' => $shareCode->getCode(),
            'message' => '🔗 Lien sécurisé généré avec succès !'
        ]);

        return $this->json($response);
    }
}
