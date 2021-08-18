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
 * Simple channel context to current channel in.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelInterface|null
     */
    private $channel = null;

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel(ChannelInterface $channel = null)
    {
        $this->channel = $channel;
    }
}
