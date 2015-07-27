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

use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Manager;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DeleteCommand implements CommandInterface
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Manager $manager
     * @return mixed
     */
    public function execute(Manager $manager)
    {
        $manager->delete($this->storage);
    }
}
