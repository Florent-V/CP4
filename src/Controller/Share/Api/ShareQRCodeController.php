<?php

namespace App\Controller\Share\Api;

use App\Enum\Role;
use App\Enum\ShareCodeType;
use App\Service\ShareCodeManager;
use App\Service\ShareEntityValidator;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Role::USER->value)]
#[Route(
    '/api/share/qrcode/{entityType}/{entityId}',
    name: 'api_share_qr_code',
    requirements: [
        'entityType' => 'splitter|expense',
        'entityId' => '.+'
    ],
    methods: ['GET']
)]
class ShareQRCodeController extends AbstractController
{
    public function __invoke(
        string $entityType,
        string $entityId,
        ShareCodeManager $shareCodeManager,
        ShareEntityValidator $validator,
        BuilderInterface $qrCodeBuilder,
        Request $request
    ): JsonResponse {
        // Valider l'entité et les permissions
        $entity = $validator->getAndValidateEntity($entityType, $entityId);

        // Générer un token sécurisé (invalide les précédents liens)
        $shareCode = $shareCodeManager->generateShareCode($entity, true, ShareCodeType::LINK);

        // Générer l'URL sécurisée
        $shareUrl = $this->generateUrl('app_join_by_secure_link', [
            'token' => $shareCode->getCode()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Créer le QR Code en utilisant le service du bundle
        $qrCode = $qrCodeBuilder->build(
            data: $shareUrl,
            size: 400,
            margin: 20
        );

        return $this->json([
            'success' => true,
            'qrImage' => $qrCode->getDataUri(),
            'shareUrl' => $shareUrl,
            'entityDisplayName' => $entity->getDisplayName(),
            'message' => '📱 QR Code généré avec succès !',
            'expiresAt' => $shareCode->getExpiresAt()->format('d/m/Y H:i')
        ]);
    }
}
