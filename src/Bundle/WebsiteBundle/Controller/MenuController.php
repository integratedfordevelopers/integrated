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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Bundle\MenuBundle\Provider\IntegratedMenuProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var IntegratedMenuProvider
     */
    private $menuProvider;

    /**
     * @var DatabaseMenuFactory
     */
    private $menuFactory;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    public function __construct(
        DocumentManager $documentManager,
        IntegratedMenuProvider $menuProvider,
        DatabaseMenuFactory $menuFactory,
        ChannelContextInterface $channelContext
    ) {
        $this->documentManager = $documentManager;
        $this->menuProvider = $menuProvider;
        $this->menuFactory = $menuFactory;
        $this->channelContext = $channelContext;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderMenu(Request $request)
    {
        $data = (array) json_decode($request->getContent(), true);
        $menu = null;

        if (isset($data['data'])) {
            $menu = $this->menuFactory->fromArray($data['data']);
        }

        return $this->render('@IntegratedWebsite/menu/render.'.$request->getRequestFormat('json').'.twig', [
            'menu' => $menu,
            'options' => isset($data['options']) ? $data['options'] : [],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $data = (array) json_decode($request->getContent(), true);

        if (isset($data['menu'])) {
            foreach ((array) $data['menu'] as $array) { // support multiple menu's
                if ($menu = $this->menuFactory->fromArray((array) $array)) {
                    if ($this->menuProvider->has($menu->getName())) {
                        $menu2 = $this->menuProvider->get($menu->getName());
                        $menu2->setChildren($menu->getChildren());
                    } else {
                        $menu->setChannel($this->channelContext->getChannel());

                        $this->documentManager->persist($menu);
                    }
                }
            }

            $this->documentManager->flush();
        }

        return new JsonResponse();
    }
}
