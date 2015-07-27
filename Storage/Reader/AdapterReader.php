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

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Identifier\IdentifierInterface;

use Gaufrette\Adapter;

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
     * @var Storage
     */
    protected $storage;

    /**
     * @var IdentifierInterface|null
     */
    protected $identifier;

    /**
     * @param Adapter $adapter
     * @param Storage $storage
     * @param IdentifierInterface $identifier
     */
    public function __construct(Adapter $adapter, Storage $storage, IdentifierInterface $identifier = null)
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
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->storage->getMetadata();
    }
}
