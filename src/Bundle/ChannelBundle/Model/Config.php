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

use DateTime;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Config implements ConfigInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var OptionsInterface
     */
    protected $options = null;

    /**
     * @var string[]
     */
    protected $channels = [];

    /**
     * @var DateTime
     */
    protected $publicationStartDate;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @var DateTime
     */
    protected $updated;

    /**
     * @param int $id
     *
     * @throws \Exception
     */
    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->created = new DateTime();
        $this->publicationStartDate = new DateTime();
        $this->updated = new DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     *
     * @return $this
     */
    public function setAdapter(string $adapter)
    {
        $this->adapter = $adapter;

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
     * @param string[]|ChannelInterface[] $channels
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
     * @param string|ChannelInterface $channel
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
     * @param string|ChannelInterface $channel
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
     * @param string|ChannelInterface $channel
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
    public function getOptions(): OptionsInterface
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
    public function getPublicationStartDate(): ?DateTime
    {
        return $this->publicationStartDate;
    }

    /**
     * @param DateTime $publicationStartDate
     */
    public function setPublicationStartDate(?DateTime $publicationStartDate): void
    {
        $this->publicationStartDate = $publicationStartDate;
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
