<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\Field\Collection;

use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DefaultTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $fields
     * @return array $return
     */
    public function transform($fields)
    {
        $return = array();
        if (is_array($fields) || $fields instanceof \Traversable) {
            foreach ($fields as $field) {
                if ($field instanceof Embedded\Field) {
                    $return[$field->getName()] = $field;
                }
            }
        }

        return $return;
    }

    /**
     * @param mixed $values
     * @return mixed|null
     */
    public function reverseTransform($values)
    {
        if (is_array($values) || $values instanceof \ArrayAccess) {
            foreach ($values as $key => $value) {
                if ($value === null) {
                    unset($values[$key]);
                }

                if ($value instanceof Embedded\CustomField) {
                    unset($values[$key]);
                }
            }
            return $values;
        }

        return null;
    }
}
