<?php

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document\Property;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\PropertyInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class EmbedManyProperty implements PropertyInterface
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var EmbedMany
     */
    protected $embedMany;

    /**
     * @param \ReflectionProperty $property
     * @param EmbedMany $embedMany
     */
    public function __construct(\ReflectionProperty $property, EmbedMany $embedMany)
    {
        $this->property = $property;
        $this->embedMany = $embedMany;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyName()
    {
        return $this->property->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getFileId(array $document)
    {
        if (isset($document[$this->getPropertyName()])) {
            var_dump($document);
            var_dump($this);
            exit();
        }
    }
}
