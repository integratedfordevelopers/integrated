<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Document;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Form\Mapping\Annotations as Type;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Document type File
 *
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("File")
 */
class File extends Content
{
    use FileTrait;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
