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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata;
use Integrated\Common\Storage\Identifier\IdentifierInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Mapping\MetadataInterface;

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
     * @var string
     */
    private $data;

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
        if (null == $this->data) {
            $file = new \SplFileObject($this->uploadedFile->getPathname(), 'r');
            // Read the file buffered
            while ($data = $file->fread(1024)) {
                $this->data .= $data;
            }
            // Cleanup
            unset($file);
        }

        return $this->data;
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
