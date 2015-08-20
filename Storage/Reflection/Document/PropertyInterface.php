<?php

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Document;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface PropertyInterface
{
    /**
     * @return string
     */
    public function getPropertyName();

    /**
     * @param array $document
     * @return string|bool
     */
    public function getFileId(array $document);
}
