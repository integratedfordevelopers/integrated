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

use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Common\Content\ContentInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
final class ContentHistoryEvent extends Event
{
    const INSERT = 'insert';

    const UPDATE = 'update';

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
     * @var array
     */
    protected $originalData;

    /**
     * @param ContentHistory   $contentHistory
     * @param ContentInterface $document
     * @param array            $originalData
     */
    public function __construct(ContentHistory $contentHistory, ContentInterface $document, array $originalData = [])
    {
        $this->contentHistory = $contentHistory;
        $this->document = $document;
        $this->originalData = $originalData;
    }

    /**
     * @return ContentHistory
     */
    public function getContentHistory()
    {
        return $this->contentHistory;
    }

    /**
     * Get the current document (with changes).
     *
     * @return ContentInterface
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Get the original data (without changes).
     *
     * @return array
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }
}
