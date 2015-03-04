<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;

use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;

/**
 * Annotation driver
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {

            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

            /** @var \Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug'
            );

            if (null !== $annotation) {
                $propertyMetadata->slugFields = $annotation->fields;
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}
