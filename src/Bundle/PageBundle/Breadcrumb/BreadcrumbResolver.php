<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Breadcrumb;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbResolver
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var BreadcrumbItem[]
     */
    protected $breadcrumbItems = null;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @param ChannelContextInterface $channelContext
     * @param RequestStack            $requestStack
     * @param DocumentManager         $documentManager
     * @param UrlResolver             $urlResolver
     */
    public function __construct(DocumentManager $documentManager, UrlResolver $urlResolver, ChannelContextInterface $channelContext, RequestStack $requestStack)
    {
        $this->documentManager = $documentManager;
        $this->urlResolver = $urlResolver;
        $this->channelContext = $channelContext;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function getBreadcrumb()
    {
        if (isset($this->breadcrumbItems)) {
            return $this->breadcrumbItems;
        }

        $channel = $this->channelContext->getChannel();
        $this->breadcrumbItems = [];

        if (!$channel instanceof ChannelInterface) {
            return $this->breadcrumbItems;
        }

        $pageRepository = $this->documentManager->getRepository(Page::class);
        $contentRepository = $this->documentManager->getRepository(Content::class);

        $url = $this->request->getPathInfo();

        $parts = explode('/', $url);
        $i = 0;
        foreach ($parts as $part) {
            $url = implode('/', \array_slice($parts, 0, ++$i));
            if ($url === '') {
                $url = '/';
            }

            //support Page
            if ($page = $pageRepository->findOneBy(['path' => $url, 'channel.$id' => $channel->getId()])) {
                /* @var Page $page */
                $this->breadcrumbItems[] = new BreadcrumbItem($page->getTitle(), $page->getPath());
                continue;
            }

            //support Content
            if (!empty($part) && $content = $contentRepository->findOneBy(['slug' => $part, 'channels.$id' => $channel->getId()])) {
                /* @var Content $content */
                if ($content->isPublished() && strpos($this->urlResolver->generateUrl($content), $url) !== false) {
                    $this->breadcrumbItems[] = new BreadcrumbItem((string) $content, $url);
                }
                continue;
            }
        }

        return $this->breadcrumbItems;
    }
}
