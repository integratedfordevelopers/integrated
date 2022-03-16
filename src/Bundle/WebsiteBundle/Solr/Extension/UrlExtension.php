<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Solr\Extension;

use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlExtension implements TypeExtensionInterface
{
    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    /**
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ChannelableInterface || !$data instanceof ContentInterface) {
            return;
        }

        foreach ($data->getChannels() as $channel) {
            $url = $this->urlResolver->generateUrl($data, $channel->getId());

            // remove app_*.php
            $url = preg_replace('/\/app_(.+?)\.php/', '', $url);

            $container->set(sprintf('url_%s', $channel->getId()), $url);
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
