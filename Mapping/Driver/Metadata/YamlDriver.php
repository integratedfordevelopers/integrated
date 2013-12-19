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

use Integrated\Bundle\SolrBundle\Mapping\Metadata;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;
use Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator;

/**
 * YamlDriver for mapping Solr config
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class YamlDriver implements DriverInterface
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
     * @return mixed|void
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        // Get files in directory with extension
        if ($files = $this->fileLocator->getFiles($this->getExtension())) {

            /* @var $file SplFileInfo */
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
        // Parse data
        if ($data = Yaml::parse($file)) {

            foreach ($data as $class => $document) {

                // Create new Metadata
                $metadata = new Metadata\Metadata($class);
                $metadata->setIndex(!empty($document['index']));

                // Loop fields if set and is array
                if ((isset($document['fields'])) && is_array($document['fields'])) {

                    foreach ($document['fields'] as $name => $field) {
                        $metadata->addField(
                            new Metadata\MetadataField(
                                $name,
                                !empty($field['index']),
                                !empty($field['facet']),
                                !empty($field['sort']),
                                !empty($field['display'])
                            )
                        );
                    }
                }

                // Add metadata to loaded metadata
                $this->metadata[$class] = $metadata;
            }
        }
    }

    /**
     * @return string
     */
    protected function getExtension()
    {
        return 'yml';
    }
}