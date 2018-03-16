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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Storage\Database\DatabaseInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DoctrineODMDatabase implements DatabaseInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        return $this->dm
            ->getConnection()
            ->selectCollection(
                $this->dm->getConfiguration()->getDefaultDB(),
                'content'
            )
            ->find()
            ->getMongoCursor()
            ->batchSize(100)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRow(array $row)
    {
        return $this->dm->getConnection()
            // Use parameters for the database
            ->selectCollection(
                $this->dm->getConfiguration()->getDefaultDB(),
                'content'
            )
            ->update(['_id' => $row['_id']], $row);
    }

    /**
     * @{@inheritdoc}
     */
    public function getObjects()
    {
        return $this->dm
            ->getUnitOfWork()
            ->getDocumentPersister(Content::class)
            ->loadAll()
            ->batchSize(100)
        ;
    }

    /**
     * @param object $object
     */
    public function saveObject($object)
    {
        $this->dm->persist($object);
        $this->dm->flush($object);
    }
}
