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

use Doctrine\ODM\MongoDB\DocumentManager;
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
     * @param ContainerInterface $containerInterface
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    /**
     * @return File[]
     */
    public function getObjects()
    {
        return $this->container->get('doctrine_mongodb.odm.document_manager')
            ->getUnitOfWork()
            ->getDocumentPersister('Integrated\Bundle\StorageBundle\Document\File')
            ->loadAll();
    }

    /**
     * @param File $file
     */
    public function saveObject(File $file)
    {
        $this->container->get('doctrine_mongodb.odm.document_manager')
            ->persist($file);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->container->get('doctrine_mongodb.odm.document_manager')->flush();
        $this->container->get('doctrine_mongodb.odm.document_manager')->clear();
    }
}
