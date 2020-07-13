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
trait RegistryTrait
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions = [];

    public function addExtension(ExtensionInterface $extension)
    {
        $name = $extension->getName();

        if (isset($this->extensions[$name])) {
            throw new \LogicException();
        }

        $this->extensions[$name] = $extension;

        return $this;
    }
}
