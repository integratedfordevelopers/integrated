<?php

namespace Integrated\Bundle\StorageBundle\Storage\Collection\Walk;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\DoctrineDocument;
use Integrated\Common\Storage\Database\DatabaseInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DocumentWalk
{
    public static function save(DatabaseInterface $database)
    {
        return function(DoctrineDocument $document) use ($database) {
            if ($document->hasUpdates()) {
                $database->saveObject($document->getDocument());
            }
        };
    }
}
