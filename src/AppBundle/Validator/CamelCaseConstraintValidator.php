<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
/**
 * Created by PhpStorm.
 * User: constantin.andreescu
 * Date: 7/24/2017
 * Time: 9:26 AM
 */

class CamelCaseConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (true) {
            $violation = $this->context->buildViolation($constraint->message);
            $violation//->setParameter('input', $value)
                      ->addViolation();
        }
    }

}