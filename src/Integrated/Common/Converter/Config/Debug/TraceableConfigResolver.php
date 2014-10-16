<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Config\Debug;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\ConfigResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TraceableConfigResolver extends ConfigResolver
{
    /**
     * {@inheritdoc}
     */
    protected function newInstance($class, array $types, ConfigInterface $parent = null)
    {
        return $this->setInstance($class, new TraceableConfig($class, $types, $parent));
    }
}
