<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class ManagerValidator extends ConstraintValidator
{
    /**
     * @var PropertyAccessor
     */
    private $accessor = null;

    /**
     * @param object     $object
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($object, Constraint $constraint)
    {
        /** @var $constraint ManagerConstraint */
        if (!\is_array($constraint->fields) && !\is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        $fields = (array) $constraint->fields;

        if (!\count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        $accessor = $this->getPropertyAccessor();
        $criteria = [];

        foreach ($fields as $fieldName) {
            if (!$accessor->isReadable($object, $fieldName)) {
                throw new ConstraintDefinitionException(sprintf("The field '%s' is not readable, so its value can not be determent.", $fieldName));
            }

            $criteria[$fieldName] = $accessor->getValue($object, $fieldName);
        }

        $result = $constraint->manger->{$constraint->method}($criteria);

        if (!\is_array($result) && !$result instanceof \Iterator) {
            $result = [$result];
        }

        // at most it will run over 2 values before exiting the foreach. Unless for some
        // strange reason the same object is in the array more then once.

        foreach ($result as $row) {
            if ($object === $row) {
                continue;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->atPath($fields[0])
                ->setInvalidValue($criteria[$fields[0]])
                ->addViolation()
            ;

            return;
        }
    }

    /**
     * @return PropertyAccessor
     */
    public function getPropertyAccessor()
    {
        if ($this->accessor === null) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor;
    }
}
