<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Mapping\Property;

use Integrated\Bundle\StorageBundle\Storage\Mapping\PropertyInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class EmbedOne implements PropertyInterface
{
    /**
     * @var string
     */
    protected $property;

    /**
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyName()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileId(array $document)
    {
        if (isset($document[$this->property]['$id'])) {
            return $document[$this->property]['$id'];
        } elseif (isset($document[$this->property]['_id'])) {
            return $document[$this->property]['_id'];
        } elseif (isset($document['_id'])) {
            return $document['_id'];
        }

        return null;
    }
}
