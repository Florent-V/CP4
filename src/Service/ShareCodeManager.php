<?php

namespace App\Service;

use App\Entity\ShareCode;
use App\Enum\ShareCodeType;
use App\Repository\ShareCodeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;

class ShareCodeManager
{
    private EntityManagerInterface $entityManager;
    private ShareCodeRepository $shareCodeRepository;
    private int $expirationDuration;
    private int $codeLength;
    private int $tokenLength;
    private int $maxAttempts;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShareCodeRepository $shareCodeRepository,
        int $expirationDuration,
        int $codeLength,
        int $tokenLength,
        int $maxAttempts
    ) {
        $this->entityManager = $entityManager;
        $this->shareCodeRepository = $shareCodeRepository;
        $this->expirationDuration = $expirationDuration;
        $this->codeLength = $codeLength;
        $this->tokenLength = $tokenLength;
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Génère un code de partage pour n'importe quelle entité
     */
    public function generateShareCode(
        object $entity,
        bool $invalidateExisting = true,
        ShareCodeType $type = ShareCodeType::CODE
    ): ShareCode {
        $entityType = $this->getEntityType($entity);
        $entityId = $this->getEntityId($entity);

        // Optionnellement invalider les codes existants du même type
        if ($invalidateExisting) {
            $this->shareCodeRepository->invalidateCodesForEntity($entityType, $entityId, $type->value);
        }

        $shareCode = new ShareCode();
        $shareCode->setCode($this->generateUniqueCode($type))
            ->setEntityType($entityType)
            ->setEntityId($entityId)
            ->setType($type->value)
            ->setExpiresAt(new DateTime('+' . $this->expirationDuration . ' seconds'))
            ->setIsUsed(false)
            ->setCreatedAt(new DateTime());

        $this->shareCodeRepository->save($shareCode, true);

        return $shareCode;
    }

    /**
     * Génère un code unique selon le type
     */
    private function generateUniqueCode(ShareCodeType $type): string
    {
        $attempt = 0;

        do {
            $code = $this->createCode($type);
            $attempt++;

            // Validation avec l'Enum
            $expectedLength = $this->getExpectedLength($type);
            if (!$type->isValidCode($code, $expectedLength)) {
                throw new RuntimeException(
                    'Code généré invalide pour le type ' . $type->value . ' : ' . $code
                );
            }

            if ($attempt >= $this->maxAttempts) {
                throw new RuntimeException(
                    'Impossible de générer un code unique après ' .
                    $this->maxAttempts . ' tentatives'
                );
            }
        } while ($this->shareCodeRepository->existsByCode($code));

        return $code;
    }

    /**
     * Crée un code selon le type spécifié
     * Utilise la logique métier définie dans l'Enum
     */
    private function createCode(ShareCodeType $type): string
    {
        $length = $this->getExpectedLength($type);

        return match ($type->getGenerationStrategy()) {
            'numeric_optimized' => $this->generateNumericCode($length),
            'alphanumeric_secure' => $this->generateAlphanumericToken($length),
            default => throw new RuntimeException(
                'Stratégie de génération non supportée : ' . $type->getGenerationStrategy()
            )
        };
    }

    /**
     * Génère un code numérique optimisé (mt_rand pour performance)
     */
    private function generateNumericCode(int $length): string
    {
        $maxValue = (int) str_repeat('9', $length);
        return str_pad((string) mt_rand(0, $maxValue), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Génère un token alphanumérique sécurisé (random_int pour sécurité)
     */
    private function generateAlphanumericToken(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $token = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $token;
    }

    /**
     * Retourne la longueur attendue selon le type de code
     */
    private function getExpectedLength(ShareCodeType $type): int
    {
        return match ($type) {
            ShareCodeType::CODE => $this->codeLength,
            ShareCodeType::LINK => $this->tokenLength,
        };
    }

    /**
     * Utilise un code de partage et retourne l'entité associée
     */
    public function useShareCode(string $code, ?string $usedBy = null): ?object
    {
        $shareCode = $this->shareCodeRepository->findValidCodeByCodeOnly($code);

        if (!$shareCode) {
            return null;
        }

        if (!$shareCode->isValid()) {
            return null;
        }

        // Marquer le code comme utilisé
        $shareCode->markAsUsed($usedBy);
        $this->shareCodeRepository->save($shareCode, true);

        // Retourner l'entité associée
        return $this->getEntityFromShareCode($shareCode);
    }

    /**
     * Vérifie si un code est valide pour une entité spécifique
     */
    public function isValidCodeForEntity(string $code, object $entity): bool
    {
        $entityType = $this->getEntityType($entity);
        $entityId = $this->getEntityId($entity);

        $shareCode = $this->shareCodeRepository->findValidCode($code, $entityType, $entityId);

        return $shareCode !== null && $shareCode->isValid();
    }

    /**
     * Récupère l'entité à partir d'un ShareCode
     */
    private function getEntityFromShareCode(ShareCode $shareCode): ?object
    {
        $entityType = $shareCode->getEntityType();
        $entityId = $shareCode->getEntityId();

        $repository = $this->entityManager->getRepository($entityType);

        // Gérer les différents types d'ID
        if (
            preg_match(
                '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-6][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
                $entityId
            )
        ) {
            // UUID
            return $repository->find($entityId);
        } elseif (is_numeric($entityId)) {
            // Integer ID
            return $repository->find((int) $entityId);
        } else {
            return $repository->find($entityId);
        }
    }

    /**
     * Récupère le type de l'entité (nom de classe complet)
     */
    private function getEntityType(object $entity): string
    {
        return get_class($entity);
    }

    /**
     * Récupère l'ID de l'entité sous forme de string
     */
    private function getEntityId(object $entity): string
    {
        if (!method_exists($entity, 'getId')) {
            throw new InvalidArgumentException('L\'entité doit avoir une méthode getId()');
        }

        $id = $entity->getId();

        if ($id === null) {
            throw new InvalidArgumentException('L\'entité doit avoir un ID non null');
        }

        return (string) $id;
    }

    /**
     * Récupère tous les codes actifs pour une entité
     */
    public function getActiveCodesForEntity(object $entity): array
    {
        $entityType = $this->getEntityType($entity);
        $entityId = $this->getEntityId($entity);

        return $this->shareCodeRepository->findActiveCodesForEntity($entityType, $entityId);
    }

    /**
     * Valide qu'un code correspond au format et à la longueur attendus
     * Utilise la logique métier de l'Enum ShareCodeType
     */
    public function validateCodeFormat(string $code, ShareCodeType $expectedType): bool
    {
        $expectedLength = $this->getExpectedLength($expectedType);
        return $expectedType->isValidCode($code, $expectedLength);
    }

    /**
     * Nettoie les codes expirés
     */
    public function cleanupExpiredCodes(): int
    {
        return $this->shareCodeRepository->deleteExpiredCodes();
    }

    /**
     * Invalide tous les codes pour une entité
     */
    public function invalidateCodesForEntity(object $entity): int
    {
        $entityType = $this->getEntityType($entity);
        $entityId = $this->getEntityId($entity);

        return $this->shareCodeRepository->invalidateCodesForEntity($entityType, $entityId);
    }
}
