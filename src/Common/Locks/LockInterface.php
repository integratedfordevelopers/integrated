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

use DateTime;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface LockInterface
{
    /**
     * Get the lock identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the lock request.
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Get the created time.
     *
     * @return Datetime
     */
    public function getCreated();

    /**
     * Get the expire time.
     *
     * @return Datetime
     */
    public function getExpires();
}
