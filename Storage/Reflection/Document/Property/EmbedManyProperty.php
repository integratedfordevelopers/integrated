<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document\Property;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedMany;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\PropertyInterface;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;

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
            return $document[$this->getPropertyName()];
        }

        return false;
    }
}
