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
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Common\Form\Mapping\Annotations as Type;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Document type File
 *
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\Document
 *
 * @Type\Document("File")
 */
class File extends Content
{
    /**
     * @var Storage
     * @Type\Field(type="integrated_file")
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\StorageBundle\Document\Embedded\Storage")
     */
    protected $file;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $description;

    /**
     * @return Storage|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param Storage $file
     * @return $this
     */
    public function setFile(Storage $file = null)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

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
