<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks\Provider\DBAL;

use Integrated\Common\Locks\Resource as BaseResource;
use Integrated\Common\Locks\ResourceInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Resource extends BaseResource
{
    /**
     * Convert a resource object to a string
     *
     * Serialize a resource object to a json encode string. The order of the
     * array keys are type first and then the identity which is called id to
     * save some space.
     *
     * @param ResourceInterface $resource
     * @return string
     */
    public static function serialize(ResourceInterface $resource = null)
    {
        if ($resource === null) {
            return null;
        }

        $resource = ['type' => $resource->getType(), 'id' => $resource->getIdentifier()];

        if ($resource['id'] === null) {
            unset($resource['id']);
        }

        return json_encode($resource);
    }

    /**
     * Convert a strong to a resource object
     *
     * The string is expected to be a resource object encoded by the serialize
     * method. So the json decode should have a array with a type and optionaly
     * a id key.
     *
     * @param string $serialized
     * @return self
     */
    public static function unserialize($serialized)
    {
        if ($serialized === null) {
            return null;
        }

        $resource = json_decode($serialized, true);

        return new self($resource['type'], $resource['id']);
    }
}
