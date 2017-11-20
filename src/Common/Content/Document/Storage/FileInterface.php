<?php

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
