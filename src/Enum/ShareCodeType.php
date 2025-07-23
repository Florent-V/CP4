<?php

namespace App\Enum;

enum ShareCodeType: string
{
    case CODE = 'code';  // Code à 6 chiffres pour saisie manuelle
    case LINK = 'link';  // Token long pour liens sécurisés

    /**
     * Retourne le pattern de génération selon le type
     */
    public function getGenerationPattern(): string
    {
        return match ($this) {
            self::CODE => 'numeric',  // Chiffres uniquement
            self::LINK => 'alphanumeric', // Lettres + chiffres
        };
    }

    /**
     * Valide si un code correspond au format attendu du type
     * Note: Les longueurs exactes sont maintenant configurables dans share_code.yaml
     */
    public function isValidFormat(string $code): bool
    {
        return match ($this) {
            self::CODE => preg_match('/^\d+$/', $code) === 1,  // Que des chiffres
            self::LINK => preg_match('/^[a-zA-Z0-9]+$/', $code) === 1,  // Alphanumériques
        };
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
