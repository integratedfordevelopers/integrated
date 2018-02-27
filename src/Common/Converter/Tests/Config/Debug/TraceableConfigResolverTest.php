<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config\Debug;

use Integrated\Common\Converter\Config\Debug\TraceableConfigResolver;
use Integrated\Common\Converter\Tests\Config\ConfigResolverTest;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TraceableConfigResolverTest extends ConfigResolverTest
{
    protected $CONFIG_INTERFACE = 'Integrated\\Common\\Converter\\Config\\Debug\\TraceableConfigInterface';

    protected function getInstance()
    {
        return new TraceableConfigResolver($this->provider);
    }
}
