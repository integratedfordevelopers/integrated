<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Extension;

use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\ChannelableInterface;

use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelExtension implements TypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ChannelableInterface) {
            return;
        }

        foreach ($data->getChannels() as $channel) {
            if ($channel instanceof ChannelInterface) {
                $container->add('facet_channels', $channel->getId());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }
}
