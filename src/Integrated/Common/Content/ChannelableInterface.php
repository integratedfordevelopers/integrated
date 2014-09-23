<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

use Doctrine\Common\Collections\Collection;

use Integrated\Common\Content\Channel\ChannelInterface;

/**
 * Interface for Content documents with Channels
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ChannelableInterface
{
    /**
     * Return the Channels of the document
     *
     * @return Collection
     */
    public function getChannels();

    /**
     * Set the Channels of the document
     *
     * @param Collection $channels
     * @return $this
     */
    public function setChannels(Collection $channels);

    /**
     * Add a Channel to the document
     *
     * @param ChannelInterface $channel
     * @return $this
     */
    public function addChannel(ChannelInterface $channel);

    /**
     * Remove Channel of the document
     *
     * @param ChannelInterface $channel
     * @return bool returns true if channel is removed, false otherwise
     */
    public function removeChannel(ChannelInterface $channel);
}