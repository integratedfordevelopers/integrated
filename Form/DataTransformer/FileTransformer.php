<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\DataTransformer;

use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\ManagerInterface;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FileTransformer implements DataTransformerInterface
{

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($file)
    {
        return [
            'file' => $file,
        ];
    }

    /**
     * {@inheritdoc}
     * @return StorageInterface|null
     */
    public function reverseTransform($value)
    {
        // It must be set, however for the sake of being sure
        if (isset($value['file']) && $value['file'] instanceof UploadedFile) {
            // Write and set the data in the entity
            return $this->manager->write(
                new UploadedFileReader($value['file'])
            );
        }

        return null;
    }
}
