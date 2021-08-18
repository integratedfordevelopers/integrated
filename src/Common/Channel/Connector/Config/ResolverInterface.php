<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Exception\InvalidArgumentException;
use Iterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ResolverInterface
{
    /**
     * Check if there is a config with the $name in any of the resolvers.
     *
     * @param string $name
     *
     * @return ConfigInterface
     */
    public function hasConfig($name);

    /**
     * Get the config with the given $name from the first matching resolver.
     *
     * @param string $name
     *
     * @return ConfigInterface
     *
     * @throws InvalidArgumentException if the config can not be found
     */
    public function getConfig($name);

    /**
     * Get all the configs that match the given channel.
     *
     * @param ChannelInterface $channel
     *
     * @return ConfigInterface[]|Iterator
     */
    public function getConfigs(ChannelInterface $channel);
}
