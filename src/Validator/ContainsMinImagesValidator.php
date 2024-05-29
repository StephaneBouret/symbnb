<?php

namespace App\Validator;

use App\Validator\ContainsMinImages;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ContainsMinImagesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContainsMinImages) {
            throw new UnexpectedTypeException($constraint, ContainsMinImages::class);
        }
        /* @var $constraint \App\Validator\Constraints\ContainsMinImages */
        if (null === $value || '' === $value) {
            return;
        }

        if (count($value) < $constraint->limit) {
            // the argument must be an array or an object that implements \Countable
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ limit }}', $constraint->limit)
                ->addViolation();
        }
    }
}