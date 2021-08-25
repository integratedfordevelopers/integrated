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
interface RequestInterface
{
    /**
     * Get the resource that needs to be locked.
     *
     * @return ResourceInterface
     */
    public function getResource();

    /**
     * Get the owner of the request or null if none is supplied.
     *
     * @return ResourceInterface|null
     */
    public function getOwner();

    /**
     * The lock will timeout if its not refreshed before the supplied
     * timeout if timeout is null then the lock will not expire until
     * released.
     *
     * @return int|null
     */
    public function getTimeout();
}
