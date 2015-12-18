<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Command;

use Integrated\Common\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Command\CommandInterface;
use Integrated\Common\Storage\ManagerInterface;


/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DeleteCommand implements CommandInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param ManagerInterface $manager
     */
    public function execute(ManagerInterface $manager)
    {
        $manager->delete($this->storage);
    }
}
