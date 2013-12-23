<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Converter;

use Integrated\Bundle\SolrBundle\Mapping\Metadata;
use Integrated\Common\Solr\Converter\ConverterInterface;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 * Converter for Content documents or entities
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Content implements ConverterInterface
{
    /**
     * @var Metadata\MetadataFactory
     */
    protected $metadataFactory;

    /**
     * Constructor
     *
     * @param Metadata\MetadataFactory $metadataFactory
     */
    public function __construct(Metadata\MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param object $object
     * @throws \InvalidArgumentException
     * @return Document|void
     */
    public function getDocument($object)
    {
        // Object must be an object
        if (!is_object($object)) {
            throw new \InvalidArgumentException('$object mus be an object');
        }

        // Object must be an instance of Content
        if ($object instanceof \Integrated\Common\Content\ContentInterface) {

            /* @var $metadata Metadata\Metadata */
            if ($metadata = $this->metadataFactory->build(get_class($object))) {

                // Can we index the file
                if (true === $metadata->getIndex()) {

                    // Add id
                    $fields = array(
                        'id' => $object->getId()
                    );

                    // Add fields
                    foreach ($metadata->getFields() as $field) {

                        $name = $field->getName();

                        // TODO: what should we do if getter is not defined?
                        // TODO: implement transformers
                        $method = 'get' . ucfirst($name);
                        $value = $object->$method();

                        // Display field
                        if (true === $field->getDisplay()) {
                            $fields[$name] = $value;
                        }

                        // Index field
                        if (true === $field->getIndex()) {
                            $fields["index.$name"] = $value;
                        }

                        // Facet field
                        if (true === $field->getFacet()) {
                            $fields["facet.$name"] = $value;
                        }

                        // Sort field
                        if (true === $field->getSort()) {
                            $fields["sort.$name"] = $value;
                        }
                    }

                    return new Document($fields);
                }
            }
        }
    }
}