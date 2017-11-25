<?php

/**
 * Created by PhpStorm.
 * User: constantin.andreescu
 * Date: 7/24/2017
 * Time: 9:32 AM
 */

namespace AppBundle\Validator;


/**
 * @Annotation
 * Class CamelCaseConstraint
 * @package AppBundle\Validator
 */
class CamelCaseConstraint extends \Symfony\Component\Validator\Constraint
{
    public $message = 'The name of the Genus must be camelCase (dont ask why)';
}