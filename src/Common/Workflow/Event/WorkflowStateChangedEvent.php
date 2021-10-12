<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Workflow\Event;

use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;
use Integrated\Common\Content\ContentInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class WorkflowStateChangedEvent extends Event
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @param State            $state
     * @param ContentInterface $content
     */
    public function __construct(State $state, ContentInterface $content)
    {
        $this->state = $state;
        $this->content = $content;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }
}
