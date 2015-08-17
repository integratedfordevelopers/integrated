<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\StorageBundle\Document\Embedded;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class File
{
    /**
     * @var Storage
     */
    protected $file;

    /**
     * @return Storage
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param Storage $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}
