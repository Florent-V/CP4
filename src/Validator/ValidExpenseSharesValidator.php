<?php

namespace App\Validator;

use App\Entity\Expense;
use App\Enum\SplitType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidExpenseSharesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidExpenseShares) {
            throw new UnexpectedTypeException($constraint, ValidExpenseShares::class);
        }

        if (!$value instanceof Expense) {
            return;
        }

        if ($value->getSplitType() === SplitType::EQUAL) {
            return;
        }

        $beneficiaries = $value->getBeneficiaries();
        $shares = $value->getShares();

        if (count($shares) !== count($beneficiaries)) {
            $this->context->buildViolation($constraint->messageMissingShares)
                ->addViolation();
            return;
        }

        $sum = array_sum(
            array_map(fn ($s) => $s->getShare(), $shares->toArray())
        );

        if ($value->getSplitType() === SplitType::PERCENTAGE) {
            if (abs($sum - 100.0) > 0.01) {
                $this->context->buildViolation($constraint->messagePercentage)
                    ->setParameter('{{ sum }}', number_format($sum, 2))
                    ->addViolation();
            }
        } elseif ($value->getSplitType() === SplitType::AMOUNT) {
            $total = $value->getAmount() ?? 0.0;
            if (abs($sum - $total) > 0.01) {
                $this->context->buildViolation($constraint->messageAmount)
                    ->setParameter('{{ total }}', number_format($total, 2))
                    ->setParameter('{{ sum }}', number_format($sum, 2))
                    ->setParameter('{{ devise }}', $value->getDevise() ?? '€')
                    ->addViolation();
            }
        }
    }
}
