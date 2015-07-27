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

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface ReaderInterface
{
    /**
     * @return string
     */
    public function read();

    /**
     * @return Metadata
     */
    public function getMetadata();
}
