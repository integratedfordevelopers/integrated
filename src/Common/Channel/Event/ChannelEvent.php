<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Event;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ChannelEvent extends Event
{
    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @param Channel $channel
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
