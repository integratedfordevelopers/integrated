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

use Integrated\Common\Solr\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Registry
{
    /**
     * @var callable[]
     */
    private $handlers;

    /**
     * Constructor.
     *
     * @param callable[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @param string $task
     *
     * @return bool
     */
    public function hasHandler($task)
    {
        return null !== $this->findHandler($task);
    }

    /**
     * @param string $task
     *
     * @return callable
     */
    public function getHandler($task)
    {
        if ($handler = $this->findHandler($task)) {
            return $handler;
        }

        throw new InvalidArgumentException(sprintf('Could not find a handler for task "%s"', $task));
    }

    /**
     * @return callable[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @param string $task
     *
     * @return callable|null
     */
    protected function findHandler($task)
    {
        $task = strtolower($task);

        if (isset($this->handlers[$task])) {
            return $this->handlers[$task];
        }

        return null;
    }
}
