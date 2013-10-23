<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Reader;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * Reader for metadata of the documents
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Document
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var Metadata\ContentTypeFactory
     */
    protected $contentTypeFactory;

    /**
     * @var array
     */
    protected $documents = array();

    /**
     * @var string
     */
    protected $documentClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\AbstractContent';

    /**
     * Constructor
     *
     * @param ManagerRegistry $managerRegistry
     * @param Metadata\ContentTypeFactory $metadataFactory
     */
    public function __construct(ManagerRegistry $managerRegistry, Metadata\ContentTypeFactory $contentTypeFactory)
    {
        $this->managerRegistry = $managerRegistry;
        $this->contentTypeFactory = $contentTypeFactory;
    }

    /**
     * Read all the Document Types and return their metadata
     *
     * @return array
     */
    public function readAll()
    {
        // Get list of available documents
        $classes = $this->managerRegistry->getManager()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        foreach ($classes as $class) {

            // Try to get metadata if document isn't already set
            if (!isset($this->documents[$class])) {

                // Create new reflection class
                $reflection = new \ReflectionClass($class);

                // Class should be subclass of documentClass
                if ($reflection->isSubclassOf($this->documentClass)) {

                    // Class should be instantiable
                    if ($reflection->isInstantiable()) {

                        // Get metadata
                        $metadata = $this->contentTypeFactory->build($class);
                        if (null !== $metadata) {
                            $this->documents[$class] = $metadata;
                        }
                    }
                }
            }
        }

        return $this->documents;
    }
}