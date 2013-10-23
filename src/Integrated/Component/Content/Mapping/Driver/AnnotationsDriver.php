<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Component\Content\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Integrated\Component\Content\Mapping\Metadata\Metadata;
use Integrated\Component\Content\Mapping\Annotations;

/**
 * AnnotationsDriver for mapping documents
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AnnotationsDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $documentClass = 'Integrated\\Bundle\\ContentBundle\\Mapping\\Annotations\\Document';

    /**
     * @var string
     */
    protected $fieldClass = 'Integrated\\Bundle\\ContentBundle\\Mapping\\Annotations\\Field';

    /**
     * Constructor
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Load metadata for class
     *
     * @param \ReflectionClass $class
     * @return Metadata|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /* @var $document Annotations\Document */
        $document = $this->reader->getClassAnnotation($class, $this->documentClass);
        if (null !== $document) {
            $metadata = new Metadata();
            $metadata->setName($document->getName());

            foreach ($class->getProperties() as $reflectionProperty) {
                /* @var $field Annotations\Field */
                $field = $this->reader->getPropertyAnnotation($reflectionProperty, $this->fieldClass);
                if (null !== $field) {
                    $metadata->addField(
                        $reflectionProperty->getName(),
                        array(
                            'type' => $field->getType(),
                            'label' => $field->getLabel(),
                            'required' => $field->getRequired()
                        )
                    );
                }
            }

            while ($class = $class->getParentClass()) {




                foreach ($class->getProperties() as $reflectionProperty) {
                    /* @var $field Annotations\Field */
                    $field = $this->reader->getPropertyAnnotation($reflectionProperty, 'FMS\Bundle\ContentBundle\Mapping\Annotations\Field');
                    if (null !== $field) {
                        $metadata->addField(
                            $reflectionProperty->getName(),
                            array(
                                'type' => $field->getType(),
                                'label' => $field->getLabel(),
                                'required' => $field->getRequired()
                            )
                        );
                    }
                }
            }

            return $metadata;
        }

        return null;
    }
}