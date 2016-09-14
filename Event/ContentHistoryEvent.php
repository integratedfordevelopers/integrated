<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Integrated\Common\Content\ContentInterface;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
final class ContentHistoryEvent extends Event
{
    /**
     */
    const INSERT = 'insert';

    /**
     */
    const UPDATE = 'update';

    /**
     */
    const DELETE = 'delete';

    /**
     * @var ContentHistory
     */
    protected $contentHistory;

    /**
     * @var ContentInterface
     */
    protected $document;

    /**
     * @param ContentHistory $contentHistory
     * @param ContentInterface $document
     */
    public function __construct(ContentHistory $contentHistory, ContentInterface $document)
    {
        $this->contentHistory = $contentHistory;
        $this->document = $document;
    }

    /**
     * @return ContentHistory
     */
    public function getContentHistory()
    {
        return $this->contentHistory;
    }

    /**
     * @return ContentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }
}
