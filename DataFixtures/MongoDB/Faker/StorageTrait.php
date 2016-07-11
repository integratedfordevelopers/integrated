<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Faker;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait StorageTrait
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param int $width
     * @param int $height
     * @param null $category
     * @param string $dir
     * @return Storage
     */
    public function createImage($width = 640, $height = 480, $category = null, $dir = '/tmp')
    {
        $faker = new Image();
        
        $image = $faker->image($dir, $width, $height, $category);
        
        return $this->createStorage($image);
    }

    /**
     * @param string $path
     * @param string $name
     * @return File
     */
    public function createFile($path, $name = '')
    {
        return (new File())
            ->setTitle($name)
            ->setFile($this->createStorage($path));
    }

    /**
     * @param string $path
     * @return Storage
     */
    public function createStorage($path)
    {
        return $this->getContainer()
            ->get('integrated_storage.manager')
            ->write(
                new MemoryReader(
                    // Use the file_get_contents to support local and remote (http) protocols
                    file_get_contents($path),
                    // Metadata
                    new Storage\Metadata(
                        substr($path, strrpos($path, '.') + 1),
                        mime_content_type($path),
                        new ArrayCollection(),
                        new ArrayCollection()
                    )
                )
            );
    }
}
