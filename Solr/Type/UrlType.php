<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Solr\Type;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlType implements TypeInterface
{
    /**
     * @var ChannelInterface[]|null
     */
    protected $channels = null;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    /**
     * @param DocumentManager $dm
     * @param UrlResolver $urlResolver
     */
    public function __construct(DocumentManager $dm, UrlResolver $urlResolver)
    {
        $this->dm = $dm;
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!($data instanceof Content)) {
            return;
        }

        foreach ($this->getChannels() as $channel) {
            $url = $this->urlResolver->generateUrl($data, $channel->getId());

            //remove app_*.php
            $url = preg_replace('/\/app_(.+?)\.php/', '', $url);

            $container->set(sprintf('url_%s', $channel->getId()), $url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.url';
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels()
    {
        if (null === $this->channels) {
            $this->channels = $this->dm->getRepository('IntegratedContentBundle:Channel\Channel')->findAll();
        }

        return $this->channels;
    }
}