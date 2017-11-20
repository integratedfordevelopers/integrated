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

use Integrated\Common\Converter\Config\Config;
use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TraceableConfig extends Config implements TraceableConfigInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * {@inheritdoc}
     *
     * @param string                $class
     * @param TypeConfigInterface[] $types
     * @param ConfigInterface       $parent
     */
    public function __construct($class, array $types, ConfigInterface $parent = null)
    {
        $this->class = $class;

        parent::__construct($types, $parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }
}
