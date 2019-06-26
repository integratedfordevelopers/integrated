<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Connector\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\ExporterInterface;
use Integrated\Common\Channel\Exporter\ExporterResponse;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Exporter implements ExporterInterface
{
    /**
     * @var TwitterOAuth
     */
    private $twitter;

    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @param TwitterOAuth    $twitter
     * @param ConfigInterface $config
     * @param UrlResolver     $urlResolver
     */
    public function __construct(TwitterOAuth $twitter, ConfigInterface $config, UrlResolver $urlResolver)
    {
        $this->twitter = $twitter;
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
            //already posted
            return;
        }

        $response = null;

        try {
            $postResponse = $this->twitter->post(
                'statuses/update',
                [
                    'status' => sprintf(
                        '%s %s',
                        $content->getTitle(),
                        'https://'.$channel->getPrimaryDomain().$this->urlResolver->generateUrl($content, $channel->getId())
                    ),
                ]
            );
        } catch (\Exception $e) {
            // @todo probably should log this somewhere INTEGRATED-995
            return;
        }

        if (isset($postResponse->id) && $postResponse->id) {
            $response = new ExporterResponse($this->config->getId(), $this->config->getAdapter());
            $response->setExternalId($postResponse->id);
        }

        //@todo: handle error INTEGRATED-995 when id does not exists, also include $postResponse['errors']

        return $response;
    }
}
