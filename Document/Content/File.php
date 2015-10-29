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

use Symfony\Component\HttpFoundation\File\File as UploadedFile;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * Document type File
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @Type\Document("File")
 */
class File extends Content
{
    /**
     * @var string
     */
    private $temp;

    /**
     * @var UploadedFile
     * @Type\Field(type="integrated_file")
     */
    protected $file;

    /**
     * @var string
     */
    protected $fileExtension;

    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @Slug(fields={"title"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var string
     * @Type\Field
     */
    protected $description;

    /**
     * Set the file of the document
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        // Check if there is an old file and store it in temp so it can be deleted after update
        if (is_file($this->getAbsolutePath())) {
            $this->temp = $this->getAbsolutePath();
        } else {
            $this->fileExtension = 'initial';
        }

        return $this;
    }

    /**
     * Get the file of the document
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the absolute path where uploaded files should be saved
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    /**
     * Get the upload dir for displaying uploaded files in the view
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/documents';
    }

    /**
     * Get the absolute path of an uploaded file
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->fileExtension ? null : $this->getUploadRootDir() . '/' . $this->id . '.' . $this->fileExtension;
    }

    /**
     * Get the public/web path of an uploaded file
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->fileExtension ? null : '/' . $this->getUploadDir() . '/' . $this->id . '.' . $this->fileExtension;
    }

    /**
     * preUpload
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $this->fileExtension = $this->getFile()->guessExtension();
        }
    }

    /**
     * upload
     */
    public function upload()
    {
        // Only upload if we got a file
        if (null === $this->getFile()) {
            return;
        }

        // Remove old images
        if (isset($this->temp)) {
            unlink($this->temp);
            $this->temp = null;
        }

        // Set path
        $this->fileExtension = $this->getFile()->guessExtension();

        // Move file
        $this->getFile()->move($this->getUploadRootDir(), $this->id . '.' . $this->fileExtension);

        // Unset file
        $this->setFile(null);
    }

    /**
     * storeFilenameForRemove
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * removeUpload
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
            $this->temp = null;
        }
    }

    /**
     * Get the title of the document
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the slug of the document
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get the description of the document
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description of the document
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
