<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Adaptor;

use Integrated\Common\Channel\Connector\AdaptorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * @param string $name
     *
     * @return AdaptorInterface
     */
    public function getAdaptor($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAdaptor($name);

    /**
     * @return AdaptorInterface[]
     */
    public function getAdaptors();
}
