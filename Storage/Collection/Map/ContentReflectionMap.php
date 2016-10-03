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

use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ContentReflectionMap
{
    /**
     * @param ReflectionCacheInterface $reflection
     * @return \Closure
     */
    public static function storageProperties(ReflectionCacheInterface $reflection)
    {
        return function (ContentInterface $content) use ($reflection) {
            $document = new DoctrineDocument($content);

            foreach ($reflection->getPropertyReflectionClass($document->getClassName())->getTargetProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    return $document;
                }
            }

            return false;
        };
    }
}
