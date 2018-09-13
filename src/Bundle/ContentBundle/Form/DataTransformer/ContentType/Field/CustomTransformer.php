<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\Field;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $field
     *
     * @return array
     */
    public function transform($field)
    {
        if ($field instanceof CustomField) {
            $options = $field->getOptions();

            return [
                'name' => $field->getName(),
                'type' => $field->getType(),
                'label' => $field->getLabel(),
                'required' => !empty($options['required']),
            ];
        }

        return [];
    }

    /**
     * @param mixed $value
     *
     * @return CustomField|null
     */
    public function reverseTransform($value)
    {
        if (\is_array($value)) {
            if (!isset($value['label'])) {
                return null;
            }

            if (!isset($value['type'])) {
                return null;
            }

            $field = new CustomField();

            $options = [
                'label' => $value['label'],
                'required' => !empty($value['required']),
            ];

            if (isset($value['name'])) {
                $field->setName($value['name']);
            }

            $field
                ->setType($value['type'])
                ->setOptions($options)
            ;

            return $field;
        }

        return null;
    }
}
