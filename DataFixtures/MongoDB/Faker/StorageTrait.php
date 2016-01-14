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
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait StorageTrait
{
    /**
     * @param string $path
     * @return Storage
     */
    public function createStorage($path)
    {
        return $this->getFileManager()->write(
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
     * @return ManagerInterface
     */
    protected function getFileManager()
    {
        // The check on the container aware does not seem to work, this is a work around
        if (isset($this->container)) {
            return $this->container->get('integrated_storage.manager');
        }

        throw new \LogicException(
            'LoadFixtureData class must be a instanceof ContainerAware'
        );
    }
}
