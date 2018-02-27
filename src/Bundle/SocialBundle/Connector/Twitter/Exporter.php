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
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Exporter\ExporterInterface;

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
     * @param TwitterOAuth $twitter
     */
    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * {@inheritdoc}
     */
    public function export($content, $state, ChannelInterface $channel)
    {
        if (!$content instanceof Article || $state != 'add') {
            return;
        }

        // @todo emove hardcoded URL when INTEGRATED-572 is fixed

        $this->twitter->post(
            'statuses/update',
            [
                'status' => sprintf(
                    '%s %s',
                    $content->getTitle(),
                    'http://'.$channel->getPrimaryDomain().'/content/article/'.$content->getSlug()
                ),
            ]
        );
    }
}
