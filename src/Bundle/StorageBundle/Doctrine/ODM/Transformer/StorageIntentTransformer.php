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
use Integrated\Bundle\StorageBundle\Form\Upload\StorageOriginal;
use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Bundle\StorageBundle\Storage\Mapping\MetadataFactoryInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
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
     * @var MetadataFactoryInterface
     */
    protected $metadata;

    /**
     * @param ManagerInterface         $manager
     * @param DecisionInterface        $decision
     * @param MetadataFactoryInterface $metadata
     */
    public function __construct(ManagerInterface $manager, DecisionInterface $decision, MetadataFactoryInterface $metadata)
    {
        $this->manager = $manager;
        $this->decision = $decision;
        $this->metadata = $metadata;
    }

    /**
     * @param DoctrineDocument $document
     */
    public function transform(DoctrineDocument $document)
    {
        $metadata = $this->metadata->getMetadata($document->getClassName());

        // Check if we've got a property that has
        foreach ($metadata->getProperties() as $property) {
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
            } elseif ($value instanceof StorageOriginal) {
                // Set back the original value
                $document->set($property->getPropertyName(), $value->getOriginal());
            } elseif (\is_array($value)) {
                // Only change the property whenever there's an embedded intent upload since changing triggers stuff
                $changes = 0;

                // Johnny Walker over the values
                foreach ($value as $key => $object) {
                    // Check if we want it
                    if ($object instanceof StorageIntentUpload) {
                        // Replace the value
                        $value[$key] = $this->manager->write(
                            new UploadedFileReader($object->getUploadedFile()),
                            // Use the decision map to place the file in the correct (public or private) file systems
                            $this->decision->getFilesystems($document)
                        );

                        // Thus we've got changes
                        ++$changes;
                    }
                }

                // Update the document
                if ($changes) {
                    $document->set($property->getPropertyName(), $value);
                }
            }
        }
    }
}
