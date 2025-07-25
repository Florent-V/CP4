<?php

namespace App\Enum;

/**
 * Types de codes de partage avec leurs règles métier
 * Responsabilité : Définir les caractéristiques et validations des types de codes
 */
enum ShareCodeType: string
{
    case CODE = 'code';  // Code à 6 chiffres pour saisie manuelle
    case LINK = 'link';  // Token long pour liens sécurisés

    /**
     * Retourne la stratégie de génération selon le type
     * Utilisé par ShareCodeManager pour choisir l'algorithme optimal
     */
    public function getGenerationStrategy(): string
    {
        return match ($this) {
            self::CODE => 'numeric_optimized',  // Utilise mt_rand pour performance
            self::LINK => 'alphanumeric_secure', // Utilise random_int pour sécurité
        };
    }

    /**
     * Valide si un code correspond au format attendu du type
     * Note: Les longueurs exactes sont configurables dans share_code.yaml
     */
    public function isValidFormat(string $code): bool
    {
        return match ($this) {
            self::CODE => preg_match('/^\d+$/', $code) === 1,  // Que des chiffres
            self::LINK => preg_match('/^[a-zA-Z0-9]+$/', $code) === 1,  // Alphanumériques
        };
    }

    /**
     * Valide si un code a la longueur attendue pour ce type
     */
    public function isValidLength(string $code, int $expectedLength): bool
    {
        return strlen($code) === $expectedLength;
    }

    /**
     * Validation complète d'un code (format + longueur)
     */
    public function isValidCode(string $code, int $expectedLength): bool
    {
        return $this->isValidFormat($code) && $this->isValidLength($code, $expectedLength);
    }

    /**
     * Retourne une description du type de code
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::CODE => 'Code numérique pour saisie manuelle',
            self::LINK => 'Token sécurisé pour partage par lien',
        };
    }
}
