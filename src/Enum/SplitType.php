<?php

declare(strict_types=1);

namespace App\Enum;

enum SplitType: string
{
    case EQUAL = 'equal';
    case PERCENTAGE = 'percentage';
    case AMOUNT = 'amount';

    public function label(): string
    {
        return match ($this) {
            self::EQUAL => 'Égalité',
            self::PERCENTAGE => 'Pourcentage',
            self::AMOUNT => 'Montant fixe',
        };
    }
}
