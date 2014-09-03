<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Converter;

use Integrated\Common\Solr\Converter\Converter;
use Integrated\Common\Content\ChannelInterface;
use Integrated\Common\Content\ChannelableInterface;

/**
 * Extension of default converter. This extension adds channels if object has channels
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelConverter extends Converter
{
    /**
     * {@inheritdoc}
     */
    public function getFields($object)
    {
        $fields = parent::getFields($object);

        if ($object instanceof ChannelableInterface) {

            if (!isset($fields['facet_channels'])) {
                $fields['facet_channels'] = array();
            }

            foreach ($object->getChannels() as $channel) {
                if ($channel instanceof ChannelInterface) {
                    $fields['facet_channels'][] = $channel->getId();
                }
            }
        }

        return $fields;
    }
}