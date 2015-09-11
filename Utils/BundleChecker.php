<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Utils;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 *
 * Class BundleChecker
 * @package Integrated\Bundle\BlockBundle\Utils
 */
class BundleChecker
{
    /** @var array */
    private $bundles;

    /**
     * @param array $bundles
     */
    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return bool
     */
    public function checkPageBundle()
    {
        return array_key_exists('IntegratedPageBundle', $this->bundles);
    }
}
