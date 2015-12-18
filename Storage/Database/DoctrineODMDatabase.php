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

use Bundle\StorageBundle\Document\FileInterface;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @param ContainerInterface $containerInterface
     */
    public function __construct(ContainerInterface $containerInterface)
    {
        $this->dm = $containerInterface->get('doctrine_mongodb.odm.document_manager');
    }

    /**
     * @return FileInterface[]
     */
    public function getFiles()
    {
        return $this->dm->getUnitOfWork()
            ->getDocumentPersister('Integrated\Bundle\StorageBundle\Document\File')
            ->loadAll();
    }

    /**
     * @return array
     */
    public function getFilesJson()
    {
        return $this->dm
            ->createQueryBuilder('Integrated\Bundle\StorageBundle\Document\File')
            ->hydrate(false)
            ->getQuery();
    }

    /**
     * @param FileInterface $file
     */
    public function save(FileInterface $file)
    {
        $this->dm->persist($file);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->dm->flush();
        $this->dm->clear();
    }
}
