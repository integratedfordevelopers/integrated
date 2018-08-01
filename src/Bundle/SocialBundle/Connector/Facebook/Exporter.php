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

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
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
     * @param Facebook        $facebook
     * @param ConfigInterface $config
     */
    public function __construct(Facebook $facebook, ConfigInterface $config)
    {
        $this->facebook = $facebook;
        $this->config = $config;
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
            // @todo remove hardcoded URL when INTEGRATED-572 is fixed
            $postResponse = $this->facebook->post(
                '/me/feed',
                [
                    'link' => 'http://'.$channel->getPrimaryDomain().'/content/article/'.$content->getSlug(),
                    'message' => $content->getTitle(),
                ],
                $this->config->getOptions()->get('token')
            );

            $graphNode = $postResponse->getGraphNode();
        } catch (FacebookResponseException $e) {
            // @todo probably should log this somewhere
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
