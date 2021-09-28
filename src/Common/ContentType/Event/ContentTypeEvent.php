<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Event;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypeEvent extends Event
{
    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @param ContentType $contentType
     */
    public function __construct(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
