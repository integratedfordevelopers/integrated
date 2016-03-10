<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\AbstractField;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FactoryProperty
{
    /**
     * @param \ReflectionProperty $property
     * @param AbstractField $field
     * @return PropertyInterface
     */
    public static function factory(\ReflectionProperty $property, AbstractField $field)
    {
        if ($class = self::isValid($field)) {
            return new $class($property, $field);
        }

        // Oh noes!
        throw new \InvalidArgumentException(
            sprintf('Class %s can not be resolved to a reflection property', get_class($field))
        );
    }

    /**
     * @param AbstractField $field
     * @return string|bool
     */
    public static function isValid($field)
    {
        if ($field instanceof AbstractField) {
            $class = get_class($field);
            $class = sprintf(
                'Integrated\Bundle\StorageBundle\Storage\Reflection\Document\Property\%sProperty',
                substr($class, strrpos($class, '\\') + 1)
            );

            if (class_exists($class)) {
                return $class;
            }
        }

        return false;
    }
}
