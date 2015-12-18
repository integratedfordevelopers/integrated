<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Storage\Database;

use Bundle\StorageBundle\Document\FileInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface DatabaseInterface
{
    /**
     * @return FileInterface[]
     */
    public function getFiles();

    /**
     * @param FileInterface $file
     */
    public function save(FileInterface $file);

    /**
     * Called occasionally to cleanup/flush the local entities from the manager
     * Can be left empty if not needed (ODM and ORM require it for memory issues)
     */
    public function commit();
}
