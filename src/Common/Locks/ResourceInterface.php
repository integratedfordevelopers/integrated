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
interface ResourceInterface
{
    /**
     * @param ResourceInterface $resource
     *
     * @return bool
     */
    public function equals(self $resource);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string|null
     */
    public function getIdentifier();
}
