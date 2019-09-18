<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbMenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var ItemInterface[]
     */
    protected $menus = [];

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @param FactoryInterface        $factory
     * @param ChannelContextInterface $channelContext
     * @param RequestStack            $requestStack
     * @param DocumentManager         $documentManager
     * @param UrlResolver             $urlResolver
     */
    public function __construct(FactoryInterface $factory, ChannelContextInterface $channelContext, RequestStack $requestStack, DocumentManager $documentManager, UrlResolver $urlResolver)
    {
        $this->factory = $factory;
        $this->channelContext = $channelContext;
        $this->request = $requestStack->getMasterRequest();
        $this->documentManager = $documentManager;
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        $channel = $this->channelContext->getChannel();

        if ($channel instanceof ChannelInterface) {
            $channel = $channel->getId();
        }

        if (isset($this->menus[$channel])) {
            return $this->menus[$channel];
        }

        $url = $this->request->getPathInfo();
        $menu = $this->factory->createItem($name, $options);

        $parts = explode('/', $url);
        $url = '';
        foreach ($parts as $part) {
            $url .= $part;
            $page = $this->documentManager->getRepository(Page::class)->findOneBy(['path' => ($url == '') ? '/' : $url, 'channel.$id' => $channel]);

            if ($page !== null) {
                /* @var $page Page */
                $menu->addChild(
                    $page->getTitle(),
                    ['uri' => ($url == '') ? '/' : $url]
                );
            } else {
                $contentItem = $this->documentManager->getRepository(Content::class)->findOneBy(['slug' => $part, 'channels.$id' => $channel]);
                if ($contentItem !== null && $contentItem->isPublished() && strpos($this->urlResolver->generateUrl($contentItem), $url) !== false) {
                    /* @var $page Page */
                    $menu->addChild(
                        (string) $contentItem,
                        ['uri' => $url]
                    );
                }
            }

            $url .= '/';
        }

        $this->menus[$channel] = $menu;

        return $this->menus[$channel];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return null !== $this->get($name, $options);
    }
}
