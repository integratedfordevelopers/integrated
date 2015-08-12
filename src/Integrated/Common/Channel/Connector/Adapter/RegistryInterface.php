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
use Integrated\Common\Channel\Exception\InvalidArgumentException;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * @param string $name
     *
     * @return AdapterInterface
     *
     * @throws UnexpectedTypeException if $name is not a string
     * @throws InvalidArgumentException if the adaptor could not be found
     */
    public function getAdapter($name);

    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws UnexpectedTypeException if $name is not a string
     */
    public function hasAdapter($name);

    /**
     * @return AdapterInterface[]
     */
    public function getAdapters();
}
