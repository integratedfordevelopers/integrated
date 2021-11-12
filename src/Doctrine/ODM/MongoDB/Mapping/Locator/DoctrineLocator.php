<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\MongoDB\Mapping\Locator;

use Doctrine\Persistence\Mapping\Driver\MappingDriver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DoctrineLocator implements ClassLocatorInterface
{
    /**
     * @var MappingDriver
     */
    private $driver;

    /**
     * Constructor.
     *
     * @param MappingDriver $driver
     */
    public function __construct(MappingDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassNames()
    {
        return $this->driver->getAllClassNames();
    }
}
