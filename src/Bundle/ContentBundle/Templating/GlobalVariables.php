<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Templating;

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GlobalVariables extends BaseGlobalVariables
{
    /**
     * Returns the current channel.
     *
     * @return ChannelInterface|null
     */
    public function getChannel()
    {
        if (!$this->container->has('channel.context')) {
            return null;
        }

        $context = $this->container->get('channel.context');

        if (!$context instanceof ChannelContextInterface) {
            return null;
        }

        return $context->getChannel();
    }
}
