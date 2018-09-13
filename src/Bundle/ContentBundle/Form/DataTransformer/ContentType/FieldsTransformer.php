<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType;

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded;
use Integrated\Common\ContentType\ContentTypeFieldInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldsTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $fields
     *
     * @return array $return
     */
    public function transform($fields)
    {
        $return = [
            'default' => [],
            'custom' => [],
        ];

        if (\is_array($fields) || $fields instanceof \Traversable) {
            foreach ($fields as $field) {
                if ($field instanceof Embedded\CustomField) {
                    $return['custom'][] = $field;
                } elseif ($field instanceof Embedded\Field) {
                    $return['default'][$field->getName()] = $field;
                }
            }
        }

        return $return;
    }

    /**
     * @param mixed $values
     *
     * @return array
     */
    public function reverseTransform($values)
    {
        if (!\is_array($values)) {
            return [];
        }

        if (empty($values['default'])) {
            $values['default'] = [];
        }

        if (empty($values['custom'])) {
            $values['custom'] = [];
        }

        if (!\is_array($values['default'])) {
            $values['default'] = [];
        }

        if (!\is_array($values['custom'])) {
            $values['custom'] = [];
        }

        $values = array_merge($values['default'], $values['custom']);
        foreach ($values as $key => $value) {
            if (!$value instanceof ContentTypeFieldInterface) {
                unset($values[$key]);
            }
        }

        return $values;
    }
}
