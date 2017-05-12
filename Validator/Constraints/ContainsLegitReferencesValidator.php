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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContainsLegitReferencesValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsLegitReferences) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\ContainsLegitReferences');
        }

        foreach ($value as $reference) {
            if (!$reference instanceof Content || !$this->checkRefRel($constraint->getRelation(), $reference)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ relation }}', $constraint->getRelation()->getName())
                    ->addViolation();
            }
        }
    }

    /**
     * @param Relation $relation
     * @param Content $reference
     * @return bool
     */
    public function checkRefRel(Relation $relation, Content $reference)
    {
        return $relation->getTargets()->exists(function ($key, $element) use ($reference) {
            return $element instanceof ContentType && $element->getId() === $reference->getContentType();
        });
    }
}
