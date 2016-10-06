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

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\DecisionInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileMap
{
    /**
     * @param DecisionInterface $decision
     * @param string $filesystem
     * @return \Closure
     */
    public static function documentAllowed(DecisionInterface $decision, $filesystem)
    {
        return function(DoctrineDocument $document) use ($decision, $filesystem) {
            // Check if the decision map allows the document for the filesystem
            if ($decision->getFilesystems($document->getDocument())->contains($filesystem)) {
                return $document;
            }

            return false;
        };
    }

    /**
     * @param ReflectionCacheInterface $reflection
     * @param string $filesystem
     * @return \Closure
     */
    public static function documentFilesystemContains(ReflectionCacheInterface $reflection, $filesystem)
    {
        return function (DoctrineDocument $document) use ($reflection, $filesystem) {
            foreach ($reflection->getPropertyReflectionClass($document->getClassName())->getTargetProperties() as $property) {
                /** @var StorageInterface|bool $file */
                if ($file = $document->get($property->getPropertyName())) {
                    if ($file->getFilesystems()->contains($filesystem)) {
                        return $document;
                    }
                }
            }

            return false;
        };
    }
}
