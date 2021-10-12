<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\Event;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FormEvent extends Event
{
    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * Event constructor.
     *
     * @param ContentTypeInterface $contentType
     * @param MetadataInterface    $metadata
     */
    public function __construct(ContentTypeInterface $contentType, MetadataInterface $metadata)
    {
        $this->contentType = $contentType;
        $this->metadata = $metadata;
    }

    /**
     * @return ContentTypeInterface
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
