<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\Upload;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * The parent of this class is used within the constraint validation of the Symfony form component.
 *
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageIntentUpload extends File implements StorageInterface
{
    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * @var StorageInterface|null
     */
    protected $original;

    /**
     * @param StorageInterface|null $original
     * @param UploadedFile $uploadedFile
     */
    public function __construct(StorageInterface $original = null, UploadedFile $uploadedFile)
    {
        $this->original = $original;
        $this->uploadedFile = $uploadedFile;

        parent::__construct($uploadedFile->getPathname());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->uploadedFile->getFilename();
    }

    /**
     * {@inheritdoc}
     */
    public function getPathname()
    {
        return $this->uploadedFile->getPathname();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystems()
    {
        return new ArrayCollection();
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return new Metadata(
            $this->uploadedFile->getExtension(),
            $this->uploadedFile->getMimeType(),
            new ArrayCollection(),
            new ArrayCollection()
        );
    }

    /**
     * @return StorageInterface
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }
}
