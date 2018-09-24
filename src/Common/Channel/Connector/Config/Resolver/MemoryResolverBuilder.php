<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config\Resolver;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverBuilder
{
    /**
     * @var ConfigInterface[]
     */
    private $configs = [];

    /**
     * If the channel is null then it will match very channel.
     *
     * @var string[][] | null
     */
    private $config_channels = [];

    /**
     * Add the config to the memory resolver.
     *
     * if $channel is left empty then every channel supplied will match.
     *
     * @param ConfigInterface           $config
     * @param string | ChannelInterface $channel
     *
     * @return self
     *
     * @throws UnexpectedTypeException if passed channel is invalid
     */
    public function addConfig(ConfigInterface $config, $channel = null)
    {
        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getId();
        }

        if ($channel !== null && !\is_string($channel)) {
            throw new UnexpectedTypeException($channel, 'null, string or Integrated\\Common\\Channel\\ChannelInterface');
        }

        $name = $config->getName();

        if (!array_key_exists($name, $this->configs)) {
            $this->configs[$name] = $config;
            $this->config_channels[$name] = [];
        }

        if ($channel === null) {
            $this->config_channels[$name] = null;
        } elseif ($this->config_channels[$name] !== null) {
            $this->config_channels[$name][$channel] = $channel;
        }

        return $this;
    }

    /**
     * Add the configs to the memory resolver.
     *
     * if $channel is left empty then every channel supplied will match.
     *
     * @param ConfigInterface[]         $configs
     * @param string | ChannelInterface $channel
     *
     * @return self
     *
     * @throws UnexpectedTypeException if passed channel is invalid
     */
    public function addConfigs(array $configs, $channel = null)
    {
        foreach ($configs as $config) {
            $this->addConfig($config, $channel);
        }

        return $this;
    }

    /**
     * @return MemoryResolver
     */
    public function getResolver()
    {
        return new MemoryResolver($this->configs, $this->config_channels);
    }
}
