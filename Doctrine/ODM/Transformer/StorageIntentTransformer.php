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

use Doctrine\ODM\MongoDB\UnitOfWork;

use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\ManipulatorDocument;
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
    private $manager;

    /**
     * @var DecisionInterface
     */
    private $decision;

    /**
     * @var ReflectionCacheInterface
     */
    private $reflection;

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
     * @param UnitOfWork $unitOfWork
     */
    public function storageWrite(UnitOfWork $unitOfWork)
    {
        // Process the full identity map
        foreach ($unitOfWork->getIdentityMap() as $class => $documents) {
            // This must be cached
            $reflection = $this->reflection->getPropertyReflectionClass($class);

            // Check if we've got anything on which we can reflect
            if ($properties = $reflection->getTargetProperties()) {
                foreach ($properties as $property) {
                    // Its less costly to reflect classes than documents
                    // Rather than do per class multiple documents
                    foreach ($documents as $id => $document) {
                        // Allow us to modify the document
                        $manipulator = new ManipulatorDocument($document);

                        // Extract the value
                        $value = $manipulator->get($property->getPropertyName());
                        if ($value instanceof StorageIntentUpload) {
                            // Remove the intent and make a storage object outta it
                            $manipulator->set(
                                $property->getPropertyName(),
                                // Create the file in the file system
                                $this->manager->write(
                                    new UploadedFileReader($value->getUploadedFile()),
                                    // Use the decision map to place the file in the correct (public or private) file systems
                                    $this->decision->getFilesystems($document)
                                )
                            );

                            // Tell doctrine we've changed something
                            $unitOfWork->scheduleForUpdate($document);
                        }
                    }
                }
            }
        }
    }
}
