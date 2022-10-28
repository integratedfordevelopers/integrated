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
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Bundle\SlugBundle\Mapping\ClassMetadataInterface;
use Integrated\Bundle\SlugBundle\Mapping\DriverInterface;
use Integrated\Bundle\SlugBundle\Mapping\Metadata\PropertyMetadata;
use ReflectionClass;
use ReflectionException;

class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @throws ReflectionException
     */
    public function loadMetadataForClass(string $class, ClassMetadataInterface $metadata): void
    {
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $property) {
            /** @var Slug $slug */
            if (!$slug = $this->reader->getPropertyAnnotation($property, Slug::class)) {
                continue;
            }

            $metadata->addProperty(new PropertyMetadata($class, $property->getName(), $slug->getFields(), $slug->getSeparator(), $slug->getLengthLimit()));
        }
    }
}
