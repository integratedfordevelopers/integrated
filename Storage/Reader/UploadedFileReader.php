<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reader;

use Integrated\Bundle\StorageBundle\Document\Embedded\Metadata;
use Integrated\Bundle\StorageBundle\Document\Embedded\MetadataInterface;
use Integrated\Bundle\StorageBundle\Storage\Identifier\IdentifierInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class UploadedFileReader implements ReaderInterface
{
    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * @var IdentifierInterface
     */
    protected $identifier;

    /**
     * @param UploadedFile $uploadedFile
     * @param IdentifierInterface $identifier
     */
    public function __construct(UploadedFile $uploadedFile, IdentifierInterface $identifier = null)
    {
        $this->uploadedFile = $uploadedFile;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return file_get_contents($this->uploadedFile->getPathname());
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return new Metadata(
            $this->uploadedFile->getClientOriginalExtension(),
            $this->uploadedFile->getMimeType(),
            new ArrayCollection(),
            new ArrayCollection()
        );
    }
}
