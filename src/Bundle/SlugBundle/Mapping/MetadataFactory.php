<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping;

use Integrated\Bundle\SlugBundle\Mapping\Metadata\ClassMetadata;

class MetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ClassMetadataInterface[]
     */
    private $data = [];

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function getMetadata(string $class): ClassMetadataInterface
    {
        if (isset($this->data[$class])) {
            return $this->data[$class];
        }

        return $this->data[$class] = $this->loadMetadata($class);
    }

    private function loadMetadata(string $class): ClassMetadataInterface
    {
        $this->driver->loadMetadataForClass($class, $metadata = new ClassMetadata());

        return $metadata;
    }
}
