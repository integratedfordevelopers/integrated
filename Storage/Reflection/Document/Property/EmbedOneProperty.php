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

use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\PropertyInterface;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class EmbedOneProperty implements PropertyInterface
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var EmbedOne
     */
    protected $embedOne;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param \ReflectionProperty $property
     * @param EmbedOne $embedOne
     */
    public function __construct(\ReflectionProperty $property, EmbedOne $embedOne)
    {
        $this->property = $property;
        $this->embedOne = $embedOne;
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
        if (isset($document[$this->getPropertyName()]['_id'])) {
            return $document[$this->getPropertyName()]['_id'];
        } elseif (isset($document[$this->getPropertyName()])) {
            return $document['_id'];
        }

        return false;
    }
}
