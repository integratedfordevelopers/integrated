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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface DatabaseInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @return File[]
     */
    public function getObjects();

    /**
     * @param File $file
     */
    public function saveObject(File $file);

    /**
     * @param string $class
     * @return array[]
     */
    public function getRows($class);

    /**
     * @param array $row
     */
    public function saveRow(array $row);

    /**
     * Update the content types in the database with the new class
     * @param string $oldClass
     * @param string $newClass
     */
    public function updateContentType($oldClass, $newClass);

    /**
     * Called occasionally to cleanup/flush the local entities from the manager
     * Can be left empty if not needed (ODM and ORM require it for memory issues)
     */
    public function commit();
}
