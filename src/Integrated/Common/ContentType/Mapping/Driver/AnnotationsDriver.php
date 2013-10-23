<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Integrated\Common\ContentType\Mapping\Metadata;
use Integrated\Common\ContentType\Mapping\Annotations;

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
    protected $documentClass = 'Integrated\\Common\\ContentType\\Mapping\\Annotations\\Document';

    /**
     * @var string
     */
    protected $fieldClass = 'Integrated\\Common\\ContentType\\Mapping\\Annotations\\Field';

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
     * @return Metadata\ContentType|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /* @var $document Annotations\Document */
        $document = $this->reader->getClassAnnotation($class, $this->documentClass);
        if (null !== $document) {

            $contentType = new Metadata\ContentType();
            $contentType->setClass($class->getName())->setType($document->getName());

            foreach ($class->getProperties() as $reflectionProperty) {

                /* @var $field Annotations\Field */
                $field = $this->reader->getPropertyAnnotation($reflectionProperty, $this->fieldClass);

                if (null !== $field) {
                    $contentTypeField = new Metadata\ContentTypeField();
                    $contentTypeField->setName($reflectionProperty->getName())
                        ->setType($field->getType())
                        ->setLabel($field->getLabel())
                        ->setRequired($field->getRequired());
                    $field->setType($field->getType())->setLabel($field->getLabel());
                    $contentType->addField($contentTypeField);
                }
            }

            return $contentType;
        }

        return null;
    }
}