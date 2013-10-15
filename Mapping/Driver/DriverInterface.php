<?php
namespace Integrated\Bundle\ContentBundle\Mapping\Driver;

/**
 * Interface for mapping drivers
 *
 * @package Integrated\Bundle\ContentBundle\Mapping\Driver
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface DriverInterface
{
    /**
     * @param \ReflectionClass $class
     * @return mixed
     */
    public function loadMetadataForClass(\ReflectionClass $class);
}