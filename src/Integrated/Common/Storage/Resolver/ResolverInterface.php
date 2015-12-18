<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\Resolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface ResolverInterface
{
    /**
     * @param array $options
     * @param string $identifier
     */
    public function __construct(array $options, $identifier);

    /**
     * @return string
     */
    public function getLocation();
}
