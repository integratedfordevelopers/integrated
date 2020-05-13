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
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelExtension implements TypeExtensionInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ChannelableInterface) {
            return;
        }

        $container->remove('facet_channels');

        foreach ($data->getChannels() as $channel) {
            if ($channel instanceof ChannelInterface) {
                $container->add('facet_channels', $channel->getId());
            }
        }

        if (\count($data->getChannels()) == 0 && $data instanceof ContentInterface) {
            $contentType = $this->resolver->getType($data->getContentType());
            $channelOption = $contentType->getOption('channels');
            if ($contentType->getOption('publication') !== 'disabled' && $channelOption['disabled'] == 0) {
                $container->add('facet_channels', 'None');
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
