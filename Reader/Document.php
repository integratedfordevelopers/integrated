<?php
namespace Integrated\Bundle\ContentBundle\Reader;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Mapping\Metadata;

/**
 * Reader for metadata of the documents
 *
 * @package Integrated\Bundle\ContentBundle\Reader
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
            if (!isset($this->documents[$class])) {
                $reflection = new \ReflectionClass($class);
                if ($reflection->isSubclassOf($this->documentClass)) {
                    if ($reflection->isInstantiable()) {
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