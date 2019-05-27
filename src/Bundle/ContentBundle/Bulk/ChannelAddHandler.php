<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\ContentInterface;

class ChannelAddHandler implements HandlerInterface
{
    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * Constructor.
     *
     * @param ChannelInterface $channel
     */
    public function __construct(ChannelInterface $channel)
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        if (!$content instanceof Content) {
            return;
        }

        /** @var Content $content */
        $content->addChannel($this->channel);
    }
}
