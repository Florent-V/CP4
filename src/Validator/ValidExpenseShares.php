<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ValidExpenseShares extends Constraint
{
    public string $messagePercentage = 'Les pourcentages doivent totaliser 100%. Total actuel : {{ sum }}%.';
    public string $messageAmount = 'Les montants doivent totaliser {{ total }}{{ devise }}. '
     . 'Total actuel : {{ sum }}{{ devise }}.';
    public string $messageMissingShares = 'Chaque bénéficiaire doit avoir une part définie.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
