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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\WebsiteBundle\Routing\ContentTypePageLoader;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlResolver
{
    /**
     * @var ContentTypeControllerManager
     */
    protected $controllerManager;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var ContentTypePage[]
     */
    protected $contentTypePages = [];

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param ContentTypeControllerManager $controllerManager
     * @param ChannelContextInterface      $channelContext
     * @param RouterInterface              $router
     * @param DocumentManager              $dm
     */
    public function __construct(
        ContentTypeControllerManager $controllerManager,
        ChannelContextInterface $channelContext,
        RouterInterface $router,
        DocumentManager $dm
    ) {
        $this->controllerManager = $controllerManager;
        $this->channelContext = $channelContext;
        $this->router = $router;
        $this->dm = $dm;
    }

    /**
     * Returns the correct path for symfony routing module (replace "#[string]#" with "{[string}").
     *
     * @param ContentTypePage $page
     *
     * @return string
     */
    public function getRoutePath(ContentTypePage $page)
    {
        return preg_replace_callback(
            '/(#)([\s\S]+?)(#)/',
            function ($matches) use ($page) {
                return sprintf('{%s}', $matches[2]);
            },
            $page->getPath()
        );
    }

    /**
     * @param ContentTypePage $page
     *
     * @return string
     */
    public function getRouteName(ContentTypePage $page)
    {
        return sprintf('%s_%s', ContentTypePageLoader::ROUTE_PREFIX, $page->getId());
    }

    /**
     * @param ContentInterface $document
     * @param null             $channelId
     *
     * @return string
     */
    public function generateUrl(ContentInterface $document, $channelId = null)
    {
        $page = $this->getContentTypePageById($document->getContentType(), $channelId);

        if ($page instanceof ContentTypePage) {
            return $this->getContentTypePageUrl($page, $document);
        }

        // fallback /app_*.php/content/contentType/slug, in production /content/contentType/slug
        return sprintf(
            '%s/content/%s/%s',
            $this->router->getContext()->getBaseUrl(),
            $document->getContentType(),
            //todo INTEGRATED-440 add Slug to ContentInterface
            $document->getSlug()
        );
    }

    /**
     * todo INTEGRATED-440 add Slug and getReferenceByRelationIdto ContentInterface.
     *
     * @param ContentTypePage  $page
     * @param ContentInterface $document
     *
     * @return string
     */
    public function getContentTypePageUrl(ContentTypePage $page, ContentInterface $document)
    {
        return $this->router->generate(
            $this->getRouteName($page),
            $this->getRoutingParamaters($page, $document)
        );
    }

    /**
     * @param ContentTypePage $page
     *
     * @return array
     */
    protected function getRoutingParamaters(ContentTypePage $page, ContentInterface $content)
    {
        $parameters = ['slug' => $content->getSlug()];

        foreach ($this->getRelationIds($page) as $relationId) {
            if ($relation = $content->getReferenceByRelationId($relationId)) {
                // keep track of last document
                // if there is a previous relation then the new reference should be searched that relation
                // first time use current document
                $content = $relation;

                $parameters[$relationId] = $relation->getSlug();
            } else {
                //no relation found, as fallback use relationId
                $parameters[$relationId] = $relationId;
            }
        }

        return $parameters;
    }

    /**
     * @param ContentTypePage $page
     *
     * @return array
     */
    protected function getRelationIds(ContentTypePage $page)
    {
        $relationIds = [];

        if (preg_match_all('/(#)([\s\S]+?)(#)/', $page->getPath(), $matches)) {
            foreach ($matches as $match) {
                $relationIds[] = $match[0];
            }
        }

        return $relationIds;
    }

    /**
     * @param $channelId
     * @param $contentTypeId
     *
     * @return ContentTypePage
     */
    protected function getContentTypePageById($contentTypeId, $channelId = null)
    {
        if (null === $channelId) {
            $channel = $this->channelContext->getChannel();

            if ($channel instanceof Channel) {
                $channelId = $channel->getId();
            }
        }

        if (isset($this->contentTypePages[$channelId][$contentTypeId])) {
            return $this->contentTypePages[$channelId][$contentTypeId];
        }

        return $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage')
            ->findOneBy([
                'channel.$id' => $channelId,
                'contentType.$id' => $contentTypeId,
            ]);
    }
}
