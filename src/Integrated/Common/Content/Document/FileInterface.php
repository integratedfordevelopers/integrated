<?php

namespace Bundle\StorageBundle\Document;

use Integrated\Common\Document\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface FileInterface
{
    /**
     * @return StorageInterface|null
     */
    public function getFile();
}
