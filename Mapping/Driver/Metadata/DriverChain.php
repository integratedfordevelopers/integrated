<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata;

/**
 * FileLocator for directories where YML files can be found
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DriverChain implements DriverInterface
{
    /**
     * @var DriverInterface[]
     */
    protected $drivers;

    /**
     * @param array $drivers
     */
    public function __construct(array $drivers)
    {
        $this->setDrivers($drivers);
    }

    /**
     * @param DriverInterface $driver
     * @param string $namespace
     * @return $this
     */
    public function addDriver(DriverInterface $driver, $namespace)
    {
        $this->drivers[$namespace] = $driver;
        return $this;
    }

    /**
     * @return DriverInterface[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * @param array $drivers
     * @return $this
     */
    public function setDrivers(array $drivers)
    {
        foreach ($drivers as $driver) {
            $this->addDriver($driver, get_class($driver));
        }

        return $this;
    }

    /**
     * @param \ReflectionClass $class
     * @return \Integrated\Bundle\SolrBundle\Mapping\Metadata\Metadata|null
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (is_array($this->drivers)) {

            /* @var $driver DriverInterface */
            foreach ($this->drivers as $driver) {
                if ($metadata = $driver->loadMetadataForClass($class)) {
                    return $metadata;
                }
            }
        }

        return null;
    }
}