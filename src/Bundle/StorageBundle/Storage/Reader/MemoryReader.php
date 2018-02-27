<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reader;

use Integrated\Common\Content\Document\Storage\Embedded\MetadataInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class MemoryReader implements ReaderInterface
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param string            $content
     * @param MetadataInterface $metadata
     */
    public function __construct($content, MetadataInterface $metadata)
    {
        $this->content = $content;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
