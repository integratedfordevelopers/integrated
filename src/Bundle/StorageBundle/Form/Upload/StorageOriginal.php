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

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class StorageOriginal extends File implements StorageInterface
{
    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var StorageInterface
     */
    private $original;

    /**
     * @param \SplFileInfo     $file
     * @param StorageInterface $original
     */
    public function __construct(\SplFileInfo $file, StorageInterface $original)
    {
        $this->file = $file;
        $this->original = $original;

        parent::__construct($file->getPathname());
    }

    /**
     * @return StorageInterface
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->original->getIdentifier();
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
            $this->file->getExtension(),
            mime_content_type($this->file->getPathname()),
            new ArrayCollection(),
            new ArrayCollection()
        );
    }
}
