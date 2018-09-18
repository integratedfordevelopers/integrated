<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class FromConfigCustomFieldTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($field)
    {
        if ($field === null) {
            return null;
        }

        if (!$field instanceof CustomField) {
            throw new UnexpectedTypeException($field, CustomField::class);
        }

        return [
            'label' => $field->getOptions()['label'] ?? $field->getName(),
            'type' => $field->getType()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        return new CustomField(
            (string) $value['label'],
            (string) $value['type'],
            [
                'label' => (string) $value['label'],
                'required' => false,
            ]
        );
    }
}
