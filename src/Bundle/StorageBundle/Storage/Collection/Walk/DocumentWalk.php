<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Collection\Walk;

use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Common\Storage\Database\DatabaseInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DocumentWalk
{
    /**
     * @param DatabaseInterface $database
     *
     * @return \Closure
     */
    public static function save(DatabaseInterface $database)
    {
        return function (DoctrineDocument $document) use ($database) {
            if ($document->hasUpdates()) {
                $database->saveObject($document->getDocument());
            }
        };
    }
}
