<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\FileResolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FileResolverInterface
{
    /**
     * @param array  $options
     * @param string $identifier
     */
    public function __construct(array $options, $identifier);

    /**
     * An URL send to the client (browser).
     *
     * @return string
     */
    public function getLocation();
}
