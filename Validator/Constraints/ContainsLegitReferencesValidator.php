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
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * This Validator checks if all content type(s) of the given references correspond with the target type(s) of the given Relation
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContainsLegitReferencesValidator extends ConstraintValidator
{
    /**
     * @param ContentInterface[] $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsLegitReferences) {
            throw new UnexpectedTypeException($constraint, ContainsLegitReferences::class);
        }

        if (!$constraint->relation instanceof Relation) {
            throw new UnexpectedTypeException($constraint->relation, Relation::class);
        }

        if (!is_array($value) || !$value instanceof \Traversable) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $reference) {
            if (!$reference instanceof ContentInterface) {
                throw new UnexpectedTypeException($reference, ContentInterface::class);
            }
            if (!$this->checkIfReferenceCorrespondToRelationTarget($constraint->relation, $reference)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ relation }}', $constraint->relation->getName())
                    ->addViolation();
            }
        }
    }

    /**
     * @param Relation $relation
     * @param Content $reference
     * @return bool
     */
    private function checkIfReferenceCorrespondToRelationTarget(Relation $relation, Content $reference)
    {
        return $relation->getTargets()->exists(function ($key, $element) use ($reference) {
            return $element instanceof ContentType && $element->getId() === $reference->getContentType();
        });
    }
}
