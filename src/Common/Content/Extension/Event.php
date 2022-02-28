<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Event extends \Symfony\Contracts\EventDispatcher\Event
{
    public const CONTENT = 'extension.event.content';

    public const CONTENT_TYPE = 'extension.event.contenttype';

    public const METADATA = 'extension.event.medadata';

    public const UNKNOWN = 'extension.event.unknown';

    /**
     * @var string
     */
    private $eventType;

    public function __construct($eventType = null)
    {
        $this->eventType = $eventType === null ? self::UNKNOWN : (string) $eventType;
    }

    public function getEventType()
    {
        return $this->eventType;
    }
}
