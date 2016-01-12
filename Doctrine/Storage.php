<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Doctrine;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\StorageBundle\Storage\Command\DeleteCommand;
use Integrated\Bundle\StorageBundle\Storage\Manager;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Storage
{
    /**
     * @const Repository class
     */
    const REPOSITORY = 'Integrated\Bundle\ContentBundle\Document\Content\Content';

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param DocumentManager $documentManager
     * @param StorageInterface $storage
     */
    public function delete(DocumentManager $documentManager, StorageInterface $storage)
    {
        // Query
        $result = $documentManager->getRepository(self::REPOSITORY)
            ->createQueryBuilder()
            ->field('file.identifier')->equals($storage->getIdentifier())
            ->getQuery()->execute();

        // Only delete when there is less than 2 documents (1 is the entity to deleted it self)
        if (1 == $result->count()) {
            // Lets put the delete command in a bus and send it away
            $this->manager->handle(
                new DeleteCommand($storage)
            );
        }
    }
}
