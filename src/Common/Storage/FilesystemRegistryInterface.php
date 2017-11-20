<?php

namespace Integrated\Common\Storage;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FilesystemRegistryInterface
{
    /**
     * @param string $filesystem
     * @return mixed
     */
    public function get($filesystem);

    /**
     * @return array
     */
    public function keys();

    /**
     * @param $key
     * @return bool
     */
    public function exists($key);

    /**
     * @return array
     */
    public function getIterator();
}
