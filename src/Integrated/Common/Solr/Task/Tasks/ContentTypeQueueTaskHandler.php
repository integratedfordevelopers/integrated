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
use Integrated\Common\Solr\Task\Provider\ContentTypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeQueueTaskHandler
{
    /**
     * @var ContentTypeProviderInterface
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
     * @param ContentTypeProviderInterface $provider
     * @param QueueInterface               $queue
     * @param JobFactory                   $factory
     */
    public function __construct(ContentTypeProviderInterface $provider, QueueInterface $queue, JobFactory $factory)
    {
        $this->provider = $provider;
        $this->queue = $queue;
        $this->factory = $factory;
    }

    /**
     * Queue all the content for the given content type.
     *
     * @param ContentTypeQueueTask $task
     */
    public function __invoke(ContentTypeQueueTask $task)
    {
        foreach ($this->provider->getContent($task->getId()) as $reference) {
            if (!$reference instanceof ContentInterface) {
                continue;
            }

            $this->queue->push($this->factory->create(JobFactory::ADD, $reference));
        }
    }
}
