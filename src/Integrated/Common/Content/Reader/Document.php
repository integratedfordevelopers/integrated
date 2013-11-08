<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Reader;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Integrated\Common\ContentType\Mapping\Metadata;

/**
 * Reader for metadata of the documents
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Document
{
    /**
     * @var ClassMetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var Metadata\ContentTypeFactory
     */
    protected $contentTypeFactory;

    /**
     * @var array
     */
    protected $documents = array();

    /**
     * @var array
     */
    protected $classNames;

    /**
     * @var string
     */
    protected $contentInterface = 'Integrated\Common\Content\ContentInterface';

    /**
     * Constructor
     *
     * @param ClassMetadataFactory $metadataFactory
     * @param Metadata\ContentTypeFactory $contentTypeFactory
     */
    public function __construct(ClassMetadataFactory $metadataFactory, Metadata\ContentTypeFactory $contentTypeFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->contentTypeFactory = $contentTypeFactory;
    }

    /**
     * Read all the document that implements the ContentInterface and return their metadata
     *
     * @return array
     */
    public function readAll()
    {
        // Get list of available documents
        $classes = $this->getClassNames();

        foreach ($classes as $class) {

            // Try to get metadata if document isn't already set
            if (!isset($this->documents[$class])) {

                // Create new reflection class
                $reflection = new \ReflectionClass($class);

                // Class implements ContentInterface
                if ($reflection->implementsInterface($this->contentInterface)) {

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

    /**
     * @return array
     */
    protected function getClassNames()
    {
        if (null === $this->classNames) {
            $this->classNames = array();
            foreach ($this->metadataFactory->getAllMetadata() as $metadata) {
                $this->classNames[] = $metadata->getName();
            }
        }

        return $this->classNames;
    }
}