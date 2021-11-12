<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FilesystemRegistryInterface
{
    /**
     * @param string $filesystem
     *
     * @return mixed
     */
    public function get($filesystem);

    /**
     * @return array
     */
    public function keys();

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);

    /**
     * @return array
     */
    public function getIterator();
}
