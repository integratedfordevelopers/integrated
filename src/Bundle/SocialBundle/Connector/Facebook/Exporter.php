<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Connector\Facebook;

use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\ExporterInterface;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;
use Integrated\Common\Channel\Exporter\ExporterResponse;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Exporter implements ExporterInterface
{
    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @param Facebook        $facebook
     * @param ConfigInterface $config
     * @param UrlResolver     $urlResolver
     */
    public function __construct(Facebook $facebook, ConfigInterface $config, UrlResolver $urlResolver)
    {
        $this->facebook = $facebook;
        $this->config = $config;
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function export($content, $state, ChannelInterface $channel)
    {
        if (!$content instanceof Article) {
            return;
        }

        if ($state != self::STATE_ADD) {
            return;
        }

        if ($content->hasConnector($this->config->getId())) {
            return;
        }

        try {
            $page = $this->config->getOptions()->get('page');
            $postResponse = $this->facebook->post(
                '/'.$page.'/feed',
                [
                    'link' => 'https://'.$channel->getPrimaryDomain().$this->urlResolver->generateUrl($content, $channel->getId()),
                    'message' => $content->getTitle(),
                ],
                $this->config->getOptions()->get('page_token'),
                null,
                'v3.2'
            );

            $graphNode = $postResponse->getGraphNode();
        } catch (\Exception $e) {
            // @todo probably should log this somewhere INTEGRATED-995
            return;
        }

        if (!$graphNode instanceof GraphNode) {
            throw new UnexpectedTypeException($graphNode, GraphNode::class);
        }

        $response = new ExporterResponse($this->config->getId(), $this->config->getAdapter());
        $response->setExternalId($graphNode['id']);

        return $response;
    }
}
