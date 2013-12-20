<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata;

use Symfony\Component\Finder\SplFileInfo;
use Integrated\Bundle\SolrBundle\Mapping\Metadata;
use Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator;

/**
 * XmlDriver for mapping Solr config
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class XmlDriver implements DriverInterface
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var Metadata\Metadata[]
     */
    private $metadata = array();

    /**
     * @param FileLocator $fileLocator
     */
    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param \ReflectionClass $class
     * @return Metadata\Metadata|void
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        // Get files in directory with extension
        if ($files = $this->fileLocator->getFiles($this->getExtension())) {

            /* @var $file \SplFileInfo */
            foreach ($files as $file) {
                $this->loadMetadataFromFile($file->getContents());
            }
        }

        // Return Metadata if set
        if (isset($this->metadata[$class->getName()])) {
            return $this->metadata[$class->getName()];
        }
    }

    /**
     * @param string $file
     */
    protected function loadMetadataFromFile($file)
    {
        // Create DOMDocument
        $doc = new \DOMDocument();
        $doc->loadXML($file);

        // Validate XML if XSD exsist
        if ($path = realpath(__DIR__ . '/../../../Resources/config')) {
            $xsd = $path . '/mapping.xsd';

            if (file_exists($xsd)) {
                $doc->schemaValidate($xsd);
            }
        }

        // Create DOMXPath
        $xpath = new \DOMXPath($doc);

        // Query for documents
        if ($documents = $xpath->query('/mapping/documents/document')) {
            foreach ($documents as $document) {

                // Create Metadata
                $metadata = new Metadata\Metadata($document->getAttribute('class'));
                $metadata->setIndex((bool) $document->getAttribute('index'));

                // Query for fields
                if ($fields = $xpath->query('fields/field', $document)) {
                    foreach ($fields as $field) {
                        $metadata->addField(
                            new Metadata\MetadataField(
                                $field->getAttribute('name'),
                                (bool) $field->getAttribute('index'),
                                (bool) $field->getAttribute('facet'),
                                (bool) $field->getAttribute('sort'),
                                (bool) $field->getAttribute('display')

                            )
                        );
                    }
                }

                // Add metadata to loaded metadata
                $this->metadata[$metadata->getClass()] = $metadata;
            }
        }
    }

    /**
     * @return string
     */
    protected function getExtension()
    {
        return 'xml';
    }
}