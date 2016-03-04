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

use Symfony\Component\Routing\Router;

use Integrated\Bundle\PageBundle\ContentType\ContentTypeControllerManager;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlResolver extends RouteResolver
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
     * UrlResolver constructor.
     * @param ContentTypeControllerManager $controllerManager
     * @param ChannelContextInterface $channelContext
     * @param Router $router
     * @param DocumentManager $dm
     */
    public function __construct(
        ContentTypeControllerManager $controllerManager,
        ChannelContextInterface $channelContext,
        Router $router,
        DocumentManager $dm
    ) {
        $this->controllerManager = $controllerManager;
        $this->channelContext = $channelContext;
        $this->router = $router;
        $this->dm = $dm;
    }

    /**
     * @param ContentInterface|array $document
     * @param null $channelId
     * @return string
     */
    public function generateUrl($document, $channelId = null)
    {
        if (is_array($document)) {
            return $this->getSolrUrl($document, $channelId);
        }

        $page = $this->getContentTypePageById($channelId, $document->getContentType());
dump($channelId);
dump($document->getContentType());
dump($page);
        if ($page instanceof ContentTypePage) {
            $this->setContentTypePage($page);
            return $this->resolveUrl($document);
        }

        //todo check for environment and add appp_env.php
        return sprintf('/%s/%s', $document->getContentType(), 'slug');
    }

    /**
     * @param array $document
     * @param string|null $channelId
     * @return string|null
     */
    protected function getSolrUrl($document, $channelId)
    {
        if (null === $channelId) {
            $channelId = $this->channelContext->getChannel()->getId();
        }

        $arrayKey = sprintf('url_%s', $channelId);

        if (isset($document[$arrayKey])) {
            return $document[$arrayKey];
        }

        //url is not in solr document
        return null;
    }


    /**
     * @param ContentInterface $document
     * @return string
     */
    public function resolveUrl($document)
    {
        return $this->router->generate(
            $this->getRouteName(),
            $this->resolveParameters($document)
        );
    }

    /**
     * @param $document
     * @return array
     */
    public function resolveParameters($document)
    {
        //todo INTEGRATED-440 add Slug to ContentInterface
        $parameters = ['slug' => $document->getSlug()];

        //register relations
        $this->matchRelations();

        foreach ($this->relations as $relationId) {
            //todo INTEGRATED-440 add getReferenceByRelationId to ContentInterface
            $relation = $document->getReferenceByRelationId($relationId);

            $parameters[$relationId] = $relation ? $relation->getSlug() : $relationId;
        }

        return $parameters;
    }

    /**
     * @param $channelId
     * @param $contentTypeId
     * @return ContentTypePage
     */
    protected function getContentTypePageById($contentTypeId, $channelId = null)
    {
        if (null === $channelId) {
            $channelId = $this->channelContext->getChannel()->getId();
        }

        if (isset($this->contentTypePages[$channelId][$contentTypeId])) {
            return $this->contentTypePages[$channelId][$contentTypeId];
        }

        return $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage')
            ->findOneBy([
                'channel.$id' => $channelId,
                'contentType.$id' => $contentTypeId
            ]);
    }
}