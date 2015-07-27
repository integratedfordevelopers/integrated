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

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;

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
     * @var Metadata
     */
    protected $metadata;

    /**
     * @param string $content
     * @param Metadata $metadata
     */
    public function __construct($content, Metadata $metadata)
    {
        $this->content = $content;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function read()
    {
        return $this->content;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
