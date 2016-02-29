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
     * @return string
     */
    public function generateUrl($document)
    {
        $contentType = $this->getContentType($document);
//        $slug = $this->getSlug()

        $page = $this->dm->getRepository('IntegratedPageBundle:Page\ContentTypePage')
            ->findOneBy([
                'channel.$id' => $this->channelContext->getChannel()->getId(),
                'contentType.$id' => $contentType
            ]);

        if ($page instanceof ContentTypePage) {
            $this->setContentTypePage($page);
            return $this->resolveRoute();
        }

        //todo check for environment and add appp_env.php
        return sprintf('/%s/%s', $contentType, 'slug');
//        dump();
//        $this->controllerManager->getController();
    }

    /**
     * @param ContentInterface|array $document
     * @return string
     */
    protected function getContentType($document)
    {
        if ($document instanceof ContentInterface) {
            $contentType = $document->getContentType();
        } else {
            //solr
            $contentType = isset($document['type_name']) ?: null;
        }

        if (!$contentType) {
            throw new \InvalidArgumentException('Document must contain contentType');
        }

        return $contentType;
    }
}
