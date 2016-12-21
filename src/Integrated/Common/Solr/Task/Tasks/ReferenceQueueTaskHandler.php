<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task\Tasks;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Indexer\JobFactory;
use Integrated\Common\Solr\Task\Provider\ContentProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ReferenceQueueTaskHandler
{
    /**
     * @var ContentProviderInterface
     */
    private $provider;

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var JobFactory
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param ContentProviderInterface $provider
     * @param QueueInterface           $queue
     * @param JobFactory               $factory
     */
    public function __construct(ContentProviderInterface $provider, QueueInterface $queue, JobFactory $factory)
    {
        $this->provider = $provider;
        $this->queue = $queue;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ReferenceQueueTask $task)
    {
        foreach ($this->provider->getReferenced($task->getId()) as $reference) {
            if (!$reference instanceof ContentInterface) {
                continue;
            }

            $this->queue->push($this->factory->create(JobFactory::ADD, $reference));
        }
    }
}
