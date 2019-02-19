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
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\Connector\Config\Util\ConfigIterator;
use Integrated\Common\Channel\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolver implements ResolverInterface
{
    /**
     * @var ConfigInterface[]
     */
    private $configs;

    /**
     * @var ConfigInterface[][]
     */
    private $channels = [];

    /**
     * @var ConfigInterface[]
     */
    private $defaults = [];

    /**
     * Constructor.
     *
     * @param ConfigInterface[] $configs
     * @param string[][]        $channels
     */
    public function __construct(array $configs, array $channels)
    {
        $this->configs = $configs;

        foreach ($channels as $config => $list) {
            $config = $this->getConfig($config); // will throw exception if config does not exist, which is wanted

            if (null === $list) {
                $this->defaults[] = $config;
            } else {
                foreach ($list as $channel) {
                    $this->channels[$channel][] = $config;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig($name)
    {
        return isset($this->configs[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if (isset($this->configs[$name])) {
            return $this->configs[$name];
        }

        throw new InvalidArgumentException(sprintf('Could not load config with the name "%s"', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs(ChannelInterface $channel)
    {
        $configs = $this->defaults;

        if (\array_key_exists($channel = $channel->getId(), $this->channels)) {
            $configs = array_merge($configs, $this->channels[$channel]);
        }

        return new ConfigIterator($configs);
    }
}
