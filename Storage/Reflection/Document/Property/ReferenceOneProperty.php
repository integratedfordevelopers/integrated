<?php

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document\Property;

use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\PropertyInterface;

use Doctrine\ODM\MongoDB\Mapping\Annotations\ReferenceOne;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ReferenceOneProperty implements PropertyInterface
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var ReferenceOne
     */
    protected $referenceOne;

    /**
     * @param \ReflectionProperty $property
     * @param ReferenceOne $referenceOne
     * @param mixed $value
     */
    public function __construct(\ReflectionProperty $property, ReferenceOne $referenceOne)
    {
        $this->property = $property;
        $this->referenceOne = $referenceOne;
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
        var_dump($this);
        exit();
    }
}
