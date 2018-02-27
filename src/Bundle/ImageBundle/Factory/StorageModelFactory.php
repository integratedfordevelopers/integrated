<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;
use Integrated\Bundle\ImageBundle\Model\StorageModel;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageModelFactory
{
    /**
     * @param \stdClass $json
     *
     * @return StorageModel
     */
    public static function json(\stdClass $json)
    {
        return new StorageModel(
            $json->identifier,
            $json->pathname,
            new ArrayCollection($json->filesystems),
            new Metadata(
                substr($json->pathname, strrpos($json->pathname, '.') + 1),
                $json->metadata->headers->{'Content-Type'},
                new ArrayCollection((array) $json->metadata->headers),
                new ArrayCollection()
            )
        );
    }
}
