<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Reader;

use Integrated\Bundle\SolrBundle\Mapping\Metadata;
use Integrated\Common\Content\ContentInterface;

/**
 * Reader for Solr config for Content documents or entities
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Content
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
     * @param ContentInterface $content
     * @return mixed
     */
    public function getConfig(ContentInterface $content)
    {
        $metadata = $this->metadataFactory->build(get_class($content));
        return $metadata;
    }
}