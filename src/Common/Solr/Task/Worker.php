<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task;

use Exception;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Queue\QueueMessageInterface;
use Integrated\Common\Solr\Configurable;
use Integrated\Common\Solr\Task\Event\ErrorEvent;
use Integrated\Common\Solr\Task\Event\WorkerEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Worker extends Configurable
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * Constructor.
     *
     * @param Registry       $registry
     * @param QueueInterface $queue
     */
    public function __construct(Registry $registry, QueueInterface $queue)
    {
        parent::__construct();

        $this->registry = $registry;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tasks' => 1000,
        ]);

        $resolver->setAllowedTypes('tasks', 'integer');
    }

    /**
     * Set the event dispatcher.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get the event dispatcher.
     *
     * If no event dispatcher is set then a default EventDispatcher is created.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = new EventDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * Start running the task from the queue.
     */
    public function execute()
    {
        $handled = 0;
        $handledMax = max(0, $this->getOption('tasks'));

        $dispatcher = $this->getEventDispatcher();
        $dispatcher->dispatch(Events::PRE_EXECUTE, new WorkerEvent($this));

        while (++$handled <= $handledMax && $message = $this->getMessage()) {
            try {
                \call_user_func($this->getCallable($task = $message->getPayload()), $task);
            } catch (Exception $e) {
                $dispatcher->dispatch(Events::ERROR, new ErrorEvent($this, $message, $e));
            } finally {
                $message->delete();
            }
        }

        $dispatcher->dispatch(Events::POST_EXECUTE, new WorkerEvent($this));
    }

    /**
     * @return QueueMessageInterface
     */
    protected function getMessage()
    {
        return current($this->queue->pull());
    }

    /**
     * @param object $task
     *
     * @return callable
     */
    protected function getCallable($task)
    {
        return $this->registry->getHandler(\get_class($task));
    }
}
