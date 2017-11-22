<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationNotNullValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof RelationNotNull) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\RelationNotNull');
        }

        if (null === $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ relation }}', $constraint->getRelation())
                ->addViolation();
        }
    }
}
