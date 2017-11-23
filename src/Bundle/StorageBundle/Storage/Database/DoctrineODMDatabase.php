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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Content\ContentInterface;
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
        return $this->container->get('doctrine_mongodb.odm.default_document_manager')
            ->getConnection()
            ->selectCollection(
                $this->container->get('doctrine_mongodb.odm.default_configuration')->getDefaultDB(),
                'content'
            )
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
        return $this->container->get('doctrine_mongodb.odm.default_connection')
            // Use parameters for the database
            ->selectCollection(
                $this->container->get('doctrine_mongodb.odm.default_configuration')->getDefaultDB(),
                'content'
            )
            ->update(['_id' => $row['_id']], $row);
    }

    /**
     * @return ContentInterface[]
     */
    public function getObjects()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager')
            ->getUnitOfWork()
            ->getDocumentPersister(Content::class)
            ->loadAll()
            ->batchSize(100)
            ->timeout(-1)
        ;
    }

    /**
     * @param object $object
     */
    public function saveObject($object)
    {
        $dm = $this->container->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($object);
        $dm->flush($object);
    }
}
