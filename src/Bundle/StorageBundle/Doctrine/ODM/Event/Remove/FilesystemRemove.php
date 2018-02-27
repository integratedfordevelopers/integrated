<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FilesystemRemove
{
    /**
     * @const Repository class
     */
    const REPOSITORY = 'Integrated\Bundle\ContentBundle\Document\Content\Content';

    /**
     * @param DocumentManager  $documentManager
     * @param StorageInterface $storageInterface
     *
     * @return bool
     */
    public function allow(DocumentManager $documentManager, StorageInterface $storageInterface)
    {
        // Query on the file identifier (unique/hash based filename)
        $repository = $documentManager->getRepository(self::REPOSITORY);
        $result = $repository->createQueryBuilder()
            ->field('file.identifier')->equals($storageInterface->getIdentifier())
            ->getQuery()->execute();

        return 2 < $result->count();
    }
}
