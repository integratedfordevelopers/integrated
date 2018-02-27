<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Collection\Map;

use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ContentReflectionMap
{
    /**
     * @param MetadataFactoryInterface $metadata
     *
     * @return \Closure
     */
    public static function storageProperties(MetadataFactoryInterface $metadata)
    {
        return function (ContentInterface $content) use ($metadata) {
            // Create a document with some additional methods we're gonna need
            $document = new DoctrineDocument($content);

            foreach ($metadata->getMetadata($document->getClassName())->getProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    return $document;
                }
            }

            return false;
        };
    }
}
