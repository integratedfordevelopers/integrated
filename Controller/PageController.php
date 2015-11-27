<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\MenuBundle\Provider\DatabaseMenuProvider;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Common\Content\Channel\ChannelContextInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var DatabaseMenuProvider
     */
    protected $menuProvider;

    /**
     * @var DatabaseMenuFactory
     */
    protected $menuFactory;

    /**
     * @param TwigEngine $templating
     * @param DocumentManager $dm
     * @param ChannelContextInterface $channelContext
     * @param DatabaseMenuProvider $menuProvider
     * @param DatabaseMenuFactory $menuFactory
     */
    public function __construct(TwigEngine $templating, DocumentManager $dm, ChannelContextInterface $channelContext, DatabaseMenuProvider $menuProvider, DatabaseMenuFactory $menuFactory)
    {
        $this->templating = $templating;
        $this->dm = $dm;
        $this->channelContext = $channelContext;
        $this->menuProvider = $menuProvider;
        $this->menuFactory = $menuFactory;
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Page $page)
    {
        return $this->templating->renderResponse($page->getLayout(), [
            'page' => $page,
            'integrated_block_edit' => false,
            'integrated_menu_edit' => false,
        ]);
    }

    /**
     * @param Request $request
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Page $page)
    {
        // @todo security check (INTEGRATED-383)

        if ($request->isMethod('POST')) {
            $data = (array) json_decode($request->getContent(), true);

            if (isset($data['menus'])) {
                $this->handleMenuData((array) $data['menus']);
            }

            if (isset($data['grids'])) {
                $this->handleGridData((array) $data['grids']);
            }

            $this->dm->flush();

            // @todo response 201
        }

        return $this->templating->renderResponse($page->getLayout(), [
            'page' => $page,
            'integrated_block_edit' => true,
            'integrated_menu_edit' => true,
        ]);
    }

    /**
     * @param array $data
     */
    protected function handleMenuData(array $data = [])
    {
        foreach ($data as $array) {
            if ($menu = $this->menuFactory->fromArray((array) $array)) {
                if ($menu2 = $this->menuProvider->get($menu->getName())) {
                    $menu2->setChildren($menu->getChildren());

                } else {
                    $menu->setChannel($this->channelContext->getChannel());

                    $this->dm->persist($menu);
                }
            }
        }
    }

    /**
     * @param array $data
     */
    protected function handleGridData(array $data = [])
    {

    }
}
