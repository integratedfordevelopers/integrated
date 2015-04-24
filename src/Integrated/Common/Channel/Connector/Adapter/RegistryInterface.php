<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adapter;

use Integrated\Common\Channel\Connector\AdapterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * @param string $name
     *
     * @return AdapterInterface
     */
    public function getAdapter($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAdapter($name);

    /**
     * @return AdapterInterface[]
     */
    public function getAdapters();
}
