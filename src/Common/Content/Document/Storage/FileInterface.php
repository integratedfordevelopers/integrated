<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Document\Storage;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FileInterface
{
    /**
     * @return StorageInterface
     */
    public function getFile();

    /**
     * @param StorageInterface $file
     */
    public function setFile(StorageInterface $file);
}
