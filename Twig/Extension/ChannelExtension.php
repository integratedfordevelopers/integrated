<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ChannelExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            '_channel' => $this->getChannel(),
        ];
    }

    /**
     * @return ChannelInterface | null
     */
    public function getChannel()
    {
        $context = $this->container->get('channel.context');

        if (!$context instanceof ChannelContextInterface) {
            throw new \RuntimeException('Unable to get channel context.');
        }

        return $context->getChannel();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_channel_extension';
    }
}
