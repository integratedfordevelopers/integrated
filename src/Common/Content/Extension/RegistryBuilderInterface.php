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
interface RegistryBuilderInterface
{
    /**
     * Add a extension to the registry.
     *
     * The extension name needs to be unique or an exception will be thrown
     *
     * @param ExtensionInterface $extension
     *
     * @return self
     */
    public function addExtension(ExtensionInterface $extension);

    /**
     * @return RegistryInterface
     */
    public function getRegistry();
}
