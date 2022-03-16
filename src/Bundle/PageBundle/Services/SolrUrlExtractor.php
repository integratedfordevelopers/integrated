<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Integrated\Common\Content\Channel\ChannelContextInterface;
use Solarium\QueryType\Select\Result\Document;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class SolrUrlExtractor
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param ChannelContextInterface $channelContext
     * @param RouterInterface         $router
     */
    public function __construct(ChannelContextInterface $channelContext, RouterInterface $router)
    {
        $this->channelContext = $channelContext;
        $this->router = $router;
    }

    /**
     * @param Document|array $document
     * @param string|null    $channelId
     *
     * @return string|null
     */
    public function getUrl($document, $channelId = null)
    {
        if (null === $channelId) {
            $channelId = $this->channelContext->getChannel()->getId();
        }

        $arrayKey = sprintf('url_%s', $channelId);

        if (isset($document[$arrayKey])) {
            $url = $document[$arrayKey];

            // add app_*.php if not in production
            return $this->router->getContext()->getBaseUrl().$url;
        }

        // fallback
        if (isset($document['url'])) {
            return $document['url'];
        }

        // url is not in solr document
        return null;
    }
}
