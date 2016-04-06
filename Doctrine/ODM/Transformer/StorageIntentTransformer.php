<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Doctrine\ODM\Transformer;

use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;

use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageIntentTransformer
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var DecisionInterface
     */
    protected $decision;

    /**
     * @var ReflectionCacheInterface
     */
    protected $reflection;

    /**
     * @param ManagerInterface $manager
     * @param DecisionInterface $decision
     * @param ReflectionCacheInterface $reflection
     */
    public function __construct(ManagerInterface $manager, DecisionInterface $decision, ReflectionCacheInterface $reflection)
    {
        $this->manager = $manager;
        $this->reflection = $reflection;
        $this->decision = $decision;
    }

    /**
     * @param DoctrineDocument $document
     */
    public function transform(DoctrineDocument $document)
    {
        // Fetch the reflection class from cache
        $reflection = $this->reflection->getPropertyReflectionClass($document->getClassName());

        // Check if we've got a property that has
        foreach ($reflection->getTargetProperties() as $property) {
            // Extract the value
            $value = $document->get($property->getPropertyName());
            if ($value instanceof StorageIntentUpload) {
                // Remove the intent and make a storage object outta it
                $document->set(
                    $property->getPropertyName(),
                    // Create the file in the file system
                    $this->manager->write(
                        new UploadedFileReader($value->getUploadedFile()),
                        // Use the decision map to place the file in the correct (public or private) file systems
                        $this->decision->getFilesystems($document)
                    )
                );
            }
        }
    }
}
