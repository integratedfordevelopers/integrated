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

use Integrated\Common\Content\Document\Storage\FileInterface;
use Integrated\Common\Storage\Database\DatabaseInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DoctrineODMDatabase implements DatabaseInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        return $this->getCollection()
            ->find()
            ->getMongoCursor()
            ->batchSize(100)
            ->timeout(-1)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRow(array $row)
    {
        return $this->getCollection()
            ->update(['_id' => $row['_id']], $row);
    }

    /**
     * @param string $collection
     * @return \Doctrine\MongoDB\Collection
     */
    protected function getCollection($collection = 'content')
    {
        return $this->container->get('doctrine_mongodb.odm.default_connection')
            // Use parameters for the database
            ->selectDatabase($this->container->getParameter('database_name'))
            ->selectCollection($collection);
    }

    /**
     * @return FileInterface[]
     */
    public function getObjects()
    {
        // TODO: Implement getObjects() method.
    }

    /**
     * @param FileInterface $file
     */
    public function saveObject(FileInterface $file)
    {
        // TODO: Implement saveObject() method.
    }

    /**
     * Called occasionally to cleanup/flush the local entities from the manager
     * Can be left empty if not needed (ODM and ORM require it for memory issues)
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * @param string $oldClass
     * @param string $newClass
     */
    public function updateContentType($oldClass, $newClass)
    {
        // TODO: Implement updateContentType() method.
    }
}
