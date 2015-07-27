<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Registry;

use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

/**
 * This class acts as a wrapper around the FilesystemMap (concrete but external).
 * The reason for this is the easily extend or modify this behavior in lower classes which make use of this concretion.
 * In future implementations we'll keep the possibility to add aliases or overrides (priority).
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FilesystemRegistry
{
    /**
     * @var FilesystemMap
     */
    protected $filesystemMap;

    /**
     * @param FilesystemMap $filesystemMap
     */
    public function __construct(FilesystemMap $filesystemMap)
    {
        $this->filesystemMap = $filesystemMap;
    }

    /**
     * @param $filesystem
     * @throws \InvalidArgumentException
     * @return Filesystem
     */
    public function get($filesystem)
    {
        return $this->filesystemMap->get($filesystem);
    }

    /**
     * @return array
     */
    public function keys()
    {
        $keys = [];

        foreach ($this->getIterator() as $key => $filesystem) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        foreach ($this->getIterator() as $_key => $filesystem) {
            if ($key == $_key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Filesystem[]
     */
    public function getIterator()
    {
        return $this->filesystemMap->getIterator();
    }
}
