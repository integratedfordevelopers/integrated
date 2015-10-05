<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Document;

use Integrated\Common\Content\Channel\ChannelInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Menu extends MenuItem
{
    /**
     * @var ChannelInterface
     */
    protected $channel;

    /**
     * @return ChannelInterface
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param ChannelInterface $channel
     * @return $this
     */
    public function setChannel(ChannelInterface $channel)
    {
        $this->channel = $channel;
        return $this;
    }
}
