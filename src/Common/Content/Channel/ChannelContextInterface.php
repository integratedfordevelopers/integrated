<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Channel;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ChannelContextInterface
{
    /**
     * Get the current channel.
     *
     * @return ChannelInterface|null
     */
    public function getChannel();

    /**
     * Set the current channel.
     *
     * @param ChannelInterface $channel
     */
    public function setChannel(ChannelInterface $channel = null);
}
