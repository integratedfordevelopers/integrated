<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Identifier;

use Integrated\Bundle\StorageBundle\Storage\Reader\ReaderInterface;

/**
 * Default identifier procedure, this prohibits duplicate files on the storage.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileIdentifier implements IdentifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier(ReaderInterface $reader)
    {
        return sprintf('%s.%s', md5($reader->read()), $reader->getMetadata()->getExtension());
    }
}
