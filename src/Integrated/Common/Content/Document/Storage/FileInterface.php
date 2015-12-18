<?php

namespace Integrated\Common\Document\Storage;

use Integrated\Common\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FileInterface
{
    /**
     * @return StorageInterface
     */
    public function getFile();
}
