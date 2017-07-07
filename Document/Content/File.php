<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Content\Document\Storage\FileInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * Document type File
 *
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @Type\Document("File")
 */
class File extends Content implements FileInterface
{
    /**
     * @var string
     * @Slug(fields={"title"})
     * @Type\Field
     */
    protected $slug;
    
    /**
     * @var StorageInterface
     * @Type\Field(type="Integrated\Bundle\StorageBundle\Form\Type\FileDropzoneType")
     */
    protected $file;

    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @Type\Field
     */
    protected $description;

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     * @return $this
     */
    public function setFile(StorageInterface $file = null)
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
