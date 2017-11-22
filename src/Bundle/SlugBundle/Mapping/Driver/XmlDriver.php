<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Driver;

use Metadata\Driver\DriverInterface;

/**
 * XML driver
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class XmlDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        throw new \RuntimeException('Not implemented yet'); // @todo (INTEGRATED-294)
    }
}
