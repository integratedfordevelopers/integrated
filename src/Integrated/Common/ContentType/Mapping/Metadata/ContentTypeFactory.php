<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Mapping\Metadata;

use Integrated\Common\ContentType\Mapping\Driver;

/**
 * Factory for ContentType metadata
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFactory
{
    /**
     * @var Driver\DriverInterface
     */
    protected $driver;

    /**
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * @param Driver\DriverInterface $driver
     */
    public function __construct(Driver\DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function build($className)
    {
        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        $this->loadedMetadata[$className] = $this->driver->loadMetadataForClass(new \ReflectionClass($className));

        return $this->loadedMetadata[$className];
    }
}