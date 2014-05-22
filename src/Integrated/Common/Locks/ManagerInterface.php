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
	 * Try to get a lock on the requested resource
	 *
	 * Timeout set to null is wait forever else the timeout time supplied will
	 * be used so 0 is no wait and will immediately fail if not lock can be
	 * acquired right away.
	 *
	 * @param RequestInterface $request
	 * @param int | null       $timeout null
	 *
	 * @return LockInterface
	 */
	public function acquire(RequestInterface $request, $timeout = 0);

	/**
	 * Release the lock on the resource
	 *
	 * @param LockInterface | string $lock lock object or a string with the lock id
	 */
	public function release($lock);

	/**
	 * Refresh the timeout of the lock
	 *
	 * @param LockInterface | string $lock lock object or a string with the lock id
	 * @return LockInterface
	 */
	public function refresh($lock);

//	/**
//	 * @param Resource $resource
//	 * @return Lock
//	 */
//	public function hasLock(Resource $resource);
//
//	/**
//	 * @param Resource[] $resources
//	 * @return Lock[]
//	 */
//	public function hasLocks($resources);
}