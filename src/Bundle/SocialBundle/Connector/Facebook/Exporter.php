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
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Exporter\ExporterInterface;

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
     * @var string
     */
    private $token;

    /**
     * @param Facebook $facebook
     * @param string   $token
     */
    public function __construct(Facebook $facebook, $token)
    {
        $this->facebook = $facebook;
        $this->token = $token;
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

        $this->facebook->post(
            '/me/feed',
            [
                'link' => 'http://'.$channel->getPrimaryDomain().'/content/article/'.$content->getSlug(),
                'message' => $content->getTitle(),
            ],
            $this->token
        );
    }
}
