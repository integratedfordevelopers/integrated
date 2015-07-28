<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Database;

use Integrated\Bundle\StorageBundle\Document\File;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface DatabaseInterface
{
    /**
     * @return File[]
     */
    public function getObjects();

    /**
     * @param File $file
     */
    public function saveObject(File $file);

    /**
     * Called occasionally to cleanup/flush the local entities from the manager
     * Can be left empty if not needed (ODM and ORM require it for memory issues)
     */
    public function commit();

    
}
