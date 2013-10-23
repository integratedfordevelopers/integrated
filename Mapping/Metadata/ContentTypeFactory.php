<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Metadata;

use Integrated\Bundle\ContentBundle\Mapping\Driver;

/**
 * Factory for metadata
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Metadata
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