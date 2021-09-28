<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Mapping\Event;

use Integrated\Common\Form\Mapping\MetadataEditorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataEvent extends Event
{
    /**
     * @var MetadataEditorInterface
     */
    protected $metadata;

    /**
     * @param MetadataEditorInterface $metadata
     */
    public function __construct(MetadataEditorInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return MetadataEditorInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
