<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UniqueEntryValidator extends ConstraintValidator
{
    /**
     * @var ExecutionContextInterface
     */
    protected $context;

    /**
     * @var PropertyAccessor
     */
    private $accessor = null;

    /**
     * {@inheritdoc}
     */
    public function validate($entries, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEntry) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\\UniqueEntry');
        }

        if (!\is_array($entries) && !$entries instanceof Traversable) {
            throw new UnexpectedTypeException($entries, 'array or Traversable');
        }

        $fields = (array) $constraint->fields;

        if (!\count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        $accessor = $this->getPropertyAccessor();
        $hashmap = [];

        foreach ($entries as $index => $entry) {
            $values = [];

            foreach ($fields as $fieldName) {
                if (!$accessor->isReadable($entry, $fieldName)) {
                    throw new ConstraintDefinitionException(sprintf("The field '%s' is not readable, so its value can not be determent.", $fieldName));
                }

                if (null === ($value = $accessor->getValue($entry, $fieldName))) {
                    continue;
                }

                if ($constraint->caseInsensitive) {
                    $value = strtolower($value);
                }

                $values[$fieldName] = $value;
            }

            if (!$values) {
                continue; // don't process empty values
            }

            $hash = json_encode($values);

            if (isset($hashmap[$hash])) {
                // combined field value is already encountered before so this entry is
                // not unique.

                $value = reset($values);
                $name = key($values);

                $builder = $this->context->buildViolation($constraint->message);

                self::fixViolationPath($builder);

                $builder->atPath('children['.$index.'].children['.$name.'].data')
                    ->setInvalidValue($value)
                    ->addViolation();
            }

            $hashmap[$hash] = true;
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

    /**
     * This is a work around for a possible bug.
     *
     * Seams there is a bug, or if not its working really weird, that screws up
     * the validation path big time in a collection. So we use some reflection
     * magic to fix this.
     *
     * @param ConstraintViolationBuilderInterface $builder
     */
    private static function fixViolationPath(ConstraintViolationBuilderInterface $builder)
    {
        $reflection = new \ReflectionClass($builder);

        if ($reflection->hasProperty('propertyPath')) {
            $prop = $reflection->getProperty('propertyPath');
            $prop->setAccessible(true);

            $value = $prop->getValue($builder);

            if (null !== ($pos = strpos($value, '.data'))) {
                $prop->setValue($builder, substr($value, 0, $pos));
            }
        }
    }
}
