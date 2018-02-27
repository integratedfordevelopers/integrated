<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reader;

use Gaufrette\Adapter;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Identifier\IdentifierInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class AdapterReader implements ReaderInterface
{
    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var IdentifierInterface
     */
    protected $identifier;

    /**
     * @param Adapter             $adapter
     * @param StorageInterface    $storage
     * @param IdentifierInterface $identifier
     */
    public function __construct(Adapter $adapter, StorageInterface $storage, IdentifierInterface $identifier = null)
    {
        $this->adapter = $adapter;
        $this->storage = $storage;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->adapter->read($this->storage->getIdentifier());
    }

    /**
     * @return {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->storage->getMetadata();
    }
}
