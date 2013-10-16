<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Driver;

use Doctrine\Common\Annotations\Reader,
    Integrated\Bundle\ContentBundle\Mapping\Metadata\Metadata,
    Integrated\Bundle\ContentBundle\Mapping\Annotations;

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

            return $metadata;
        }

        return null;
    }
}