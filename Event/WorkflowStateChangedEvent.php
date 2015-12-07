<?php

namespace Integrated\Bundle\WorkflowBundle\Event;

use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\Event;

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
     * @param State $state
     * @param ContentInterface $content
     */
    public function __construct(State $state, ContentInterface $content)
    {
        $this->state = $state;
        $this->content = $content;
    }

    public function getState()
    {
        return $this->state;
    }


    public function getContent()
    {
        return $this->content;
    }
}