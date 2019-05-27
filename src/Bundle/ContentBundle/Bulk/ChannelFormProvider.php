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

use Integrated\Bundle\ContentBundle\Form\Type\BulkActionChannelType;
use Integrated\Common\Bulk\Form\Config;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;

class ChannelFormProvider implements ConfigProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfig(array $content)
    {
        $config = [];

        $config[] = new Config(
            ChannelAddHandler::class,
            'addChannel',
            BulkActionChannelType::class,
            [
                'channel_handler' => ChannelAddHandler::class,
                'label' => 'Add channel',
            ],
            new ChannelFormActionMatcher(ChannelAddHandler::class)
        );

        $config[] = new Config(
            ChannelRemoveHandler::class,
            'removeChannel',
            BulkActionChannelType::class,
            [
                'channel_handler' => ChannelAddHandler::class,
                'label' => 'Remove channel',
            ],
            new ChannelFormActionMatcher(ChannelAddHandler::class)
        );

        return $config;
    }
}
