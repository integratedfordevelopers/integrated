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

use Integrated\Common\Content\Document\Storage\FileInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface DatabaseInterface
{
    /**
     * @return array
     */
    public function getRows();

    /**
     * @return FileInterface[]
     */
    public function getObjects();

    /**
     * @param FileInterface $file
     */
    public function saveObject(FileInterface $file);

    /**
     * @param array $row
     */
    public function saveRow(array $row);

    /**
     * Called occasionally to cleanup/flush the local entities from the manager
     * Can be left empty if not needed (ODM and ORM require it for memory issues)
     */
    public function commit();

    /**
     * @param string $oldClass
     * @param string $newClass
     */
    public function updateContentType($oldClass, $newClass);
}
