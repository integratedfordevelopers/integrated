<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reflection;

use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\FactoryProperty;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\PropertyInterface;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageReflection
{
    /**
     * @const The storage class to look for
     */
    const STORAGE_CLASS = 'Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage';

    /**
     * @var string
     */
    protected $className;

    /**
     * @var array|null
     */
    protected $properties;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getStorageProperties()
    {
        if (null === $this->properties) {
            // Prevent additional lookup when none is found
            $this->properties = [];

            $reader = new AnnotationReader();
            $reflection = new \ReflectionClass($this->className);

            foreach ($reflection->getProperties() as $property) {
                foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                    if (FactoryProperty::isValid($annotation)) {
                        if (self::STORAGE_CLASS == $annotation->targetDocument) {
                            // Stuff
                            $this->properties[] = FactoryProperty::factory($property, $annotation);
                        }
                    }
                }
            }
        }

        return $this->properties;
    }
}
