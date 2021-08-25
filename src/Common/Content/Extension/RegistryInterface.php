<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasExtension($name);

    /**
     * @param string $name
     *
     * @return ExtensionInterface|null
     */
    public function getExtension($name);
}
