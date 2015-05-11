<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Model;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;

use DateTime;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $adapter;

    /**
     * @var OptionsInterface
     */
    private $options = null;

    /**
     * @var string[]
     */
    private $channels = [];

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var DateTime
     */
    private $updated;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created = $this->updated = new DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     *
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->adapter = (string) $adapter;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param string[] | ChannelInterface[] $channels
     *
     * @return $this
     */
    public function setChannels($channels)
    {
        $this->channels = [];

        foreach ($channels as $channel) {
            $this->addChannel($channel);
        }

        return $this;
    }

    /**
     * @param string | ChannelInterface $channel
     *
     * @return $this
     */
    public function addChannel($channel)
    {
        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getId();
        }

        $channel = (string) $channel;

        if (false === array_search($channel, $this->channels)) {
            $this->channels[] = $channel;
        }

        return $this;
    }

    /**
     * @param string | ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannel($channel)
    {
        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getId();
        }

        $channel = (string) $channel;

        return false === array_search($channel, $this->channels) ? false : true;
    }

    /**
     * @param string | ChannelInterface $channel
     *
     * @return $this
     */
    public function removeChannel($channel)
    {
        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getId();
        }

        $channel = (string) $channel;

        if (false !== ($key = array_search($channel, $this->channels))) {
            unset($this->channels[$key]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->options = new Options();
        }

        return $this->options;
    }

    /**
     * @param OptionsInterface $options
     *
     * @return $this
     */
    public function setOptions(OptionsInterface $options = null)
    {
        if (null !== $options && !$options instanceof Options) {
            $options = new Options($options->toArray());
        }

        $this->options = $options;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     *
     * @return $this
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     *
     * @return $this
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
    }
}
