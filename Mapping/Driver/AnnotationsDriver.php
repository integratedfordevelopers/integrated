<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Integrated\Bundle\ContentBundle\Mapping\Annotations;
use Integrated\Bundle\ContentBundle\Mapping\Metadata;

/**
 * AnnotationsDriver for mapping documents
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Driver
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
     * @return Document|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /* @var $document Annotations\Document */
        $document = $this->reader->getClassAnnotation($class, $this->documentClass);
        if (null !== $document) {
            $contentType = new Metadata\ContentType();
            $contentType->setClassName($class->getName())->setClassType($document->getName());

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