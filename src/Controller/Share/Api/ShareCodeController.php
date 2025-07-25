<?php

namespace App\Controller\Share\Api;

use App\Enum\Role;
use App\Enum\ShareCodeType;
use App\Service\ShareCodeManager;
use App\Service\ShareEntityValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur API pour la génération de codes de partage à 6 chiffres
 * Fonctionne uniquement en AJAX/JSON
 */
#[IsGranted(Role::USER->value)]
#[Route(
    '/api/share/code/{entityType}/{entityId}',
    name: 'api_share_code',
    requirements: ['entityType' => 'splitter|expense', 'entityId' => '.+'],
    methods: ['POST']
)]
class ShareCodeController extends AbstractController
{
    public function __invoke(
        string $entityType,
        string $entityId,
        ShareCodeManager $shareCodeManager,
        ShareEntityValidator $validator
    ): JsonResponse {
        // Valider l'entité et les permissions
        $entity = $validator->getAndValidateEntity($entityType, $entityId);

        // Générer un code à 6 chiffres (invalide les précédents codes)
        $shareCode = $shareCodeManager->generateShareCode($entity, true, ShareCodeType::CODE);

        // Formater la réponse
        $response = $validator->formatShareResponse($entity, $shareCode, 'code', [
            'code' => $shareCode->getCode(),
            'message' => '🔑 Code de partage généré avec succès !'
        ]);

        return $this->json($response);
    }
}
