<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\SolrBundle\Mapping\Metadata;

use Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface;

/**
 * Factory for Metadata
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MetadataFactory
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
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