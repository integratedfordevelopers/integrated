<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Integrated\Common\Form\Mapping\Annotations\Document;
use Integrated\Common\Form\Mapping\Annotations\Field;
use Integrated\Common\Form\Mapping\DriverInterface;
use Integrated\Common\Form\Mapping\MetadataEditorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class AnnotationDriver implements DriverInterface
{
    public const DOCUMENT_CLASS = 'Integrated\\Common\\Form\\Mapping\\Annotations\\Document';

    public const FIELD_CLASS = 'Integrated\\Common\\Form\\Mapping\\Annotations\\Field';

    /**
     * @var MappingDriver
     */
    protected $driver;

    /**
     * @var Reader
     */
    protected $reader;

    public function __construct(MappingDriver $driver, Reader $reader)
    {
        $this->driver = $driver;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames()
    {
        return $this->driver->getAllClassNames();
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass($class, MetadataEditorInterface $metadata)
    {
        /* @var $document Document */
        $document = $this->reader->getClassAnnotation($metadata->getReflection(), self::DOCUMENT_CLASS);

        if ($document == null) {
            return;
        }

        $metadata->setType($document->getName());

        foreach ($metadata->getReflection()->getProperties() as $prop) {
            /* @var $field Field */
            $field = $this->reader->getPropertyAnnotation($prop, self::FIELD_CLASS);

            if ($field == null) {
                continue;
            }

            $metadataField = $metadata->newField($prop->getName())
                ->setType($field->getType())
                ->setOptions($field->getOptions());

            $metadata->addField($metadataField);
        }
    }
}
