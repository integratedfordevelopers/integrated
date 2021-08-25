<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Locks;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ManagerInterface
{
    /**
     * Try to get a lock on the requested resource.
     *
     * Timeout set to null is wait forever else the timeout time supplied will
     * be used so 0 is no wait and will immediately fail if not lock can be
     * acquired right away.
     *
     * @param RequestInterface $request
     * @param int|null         $timeout null
     *
     * @return LockInterface
     */
    public function acquire(RequestInterface $request, $timeout = 0);

    /**
     * Release the lock on the resource.
     *
     * @param LockInterface|string $lock lock object or a string with the lock id
     */
    public function release($lock);

    /**
     * Refresh the timeout of the lock.
     *
     * @param LockInterface|string $lock lock object or a string with the lock id
     *
     * @return LockInterface
     */
    public function refresh($lock);

    /**
     * Finds a lock by its identifier.
     *
     * @param LockInterface|string $lock lock object or a string with the lock id
     *
     * @return LockInterface
     */
    public function find($lock);

    /**
     * Finds all the locks.
     *
     * @return LockInterface[]
     */
    public function findAll();

    /**
     * Find a lock by its resource identifier.
     *
     * The result should always contain zero to one result, can't lock the same
     * resource more then once, but its returned as a array for constancy reasons.
     *
     * @param ResourceInterface $resource
     *
     * @return LockInterface[]
     */
    public function findByResource(ResourceInterface $resource);

    /**
     * Finds all the locks by its owner.
     *
     * @param ResourceInterface $resource
     *
     * @return LockInterface[]
     */
    public function findByOwner(ResourceInterface $resource);

    /**
     * Finds the locks based on the given set of filters.
     *
     * @param Filter|Filter[] $filters
     *
     * @return LockInterface[]
     */
    public function findBy($filters);

    /**
     * Remove all the locks.
     */
    public function clear();
}
