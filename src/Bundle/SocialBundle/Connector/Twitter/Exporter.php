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
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\ExporterInterface;
use Integrated\Common\Channel\Exporter\ExporterReponse;

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
     * @param TwitterOAuth    $twitter
     * @param ConfigInterface $config
     */
    public function __construct(TwitterOAuth $twitter, ConfigInterface $config)
    {
        $this->twitter = $twitter;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function export($content, $state, ChannelInterface $channel)
    {
        if (!$content instanceof Article || $state != 'add') {
            return;
        }

        if ($content->hasConnector($this->config->getId())) {
            return;
        }

        // @todo remove hardcoded URL when INTEGRATED-572 is fixed
        $postResponse = $this->twitter->post(
            'statuses/update',
            [
                'status' => sprintf(
                    '%s %s',
                    $content->getTitle(),
                    'http://'.$channel->getPrimaryDomain().'/content/article/'.$content->getSlug()
                ),
            ]
        );

        $response = new ExporterReponse($this->config->getId(), $this->config->getAdapter());
        $response->setExternalId($postResponse->getBody());

        return $response;
    }
}
