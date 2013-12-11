<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata;

use Doctrine\Common\Annotations\Reader;
use Integrated\Bundle\SolrBundle\Mapping\Metadata;
use Integrated\Bundle\SolrBundle\Mapping\Annotations;

/**
 * AnnotationsDriver for mapping Solr Config
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
    protected $documentClass = 'Integrated\\Bundle\\SolrBundle\\Mapping\\Annotations\\Document';

    /**
     * @var string
     */
    protected $fieldClass = 'Integrated\\Bundle\\SolrBundle\\Mapping\\Annotations\\Field';

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
     * @return Metadata\Metadata|void
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /* @var $document Annotations\Document */
        $document = $this->reader->getClassAnnotation($class, $this->documentClass);

        if (null !== $document) {

            // Create metadata
            $metadata = new Metadata\Metadata($class->getName());
            $metadata->setIndex($document->getIndex());

            foreach ($class->getProperties() as $reflectionProperty) {

                /* @var $field Annotations\Field */
                $field = $this->reader->getPropertyAnnotation($reflectionProperty, $this->fieldClass);

                if (null !== $field) {

                    // Add field
                    $metadata->addField(
                        new Metadata\MetadataField(
                            $reflectionProperty->getName(),
                            $field->getIndex(),
                            $field->getFacet(),
                            $field->getSort(),
                            $field->getDisplay()
                        )
                    );
                }
            }

            return $metadata;
        }
    }
}