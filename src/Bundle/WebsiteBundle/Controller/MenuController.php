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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Bundle\MenuBundle\Provider\IntegratedMenuProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderAction(Request $request)
    {
        $data = (array) json_decode($request->getContent(), true);
        $menu = null;

        if (isset($data['data'])) {
            $menu = $this->getMenuFactory()->fromArray($data['data']);
        }

        return $this->render('IntegratedWebsiteBundle:menu:render.'.$request->getRequestFormat('json').'.twig', [
            'menu' => $menu,
            'options' => isset($data['options']) ? $data['options'] : [],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $dm = $this->getDocumentManager();
        $data = (array) json_decode($request->getContent(), true);

        if (isset($data['menu'])) {
            foreach ((array) $data['menu'] as $array) { // support multiple menu's
                if ($menu = $this->getMenuFactory()->fromArray((array) $array)) {
                    if ($menu2 = $this->getMenuProvider()->get($menu->getName())) {
                        $menu2->setChildren($menu->getChildren());
                    } else {
                        $menu->setChannel($this->getChannel());

                        $dm->persist($menu);
                    }
                }
            }

            $dm->flush();
        }

        return new JsonResponse();
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @return IntegratedMenuProvider
     */
    protected function getMenuProvider()
    {
        return $this->get('integrated_menu.provider.integrated_menu_provider');
    }

    /**
     * @return DatabaseMenuFactory
     */
    protected function getMenuFactory()
    {
        return $this->get('integrated_menu.menu.database_menu_factory');
    }

    /**
     * @return ChannelInterface|null
     */
    protected function getChannel()
    {
        return $this->get('channel.context')->getChannel();
    }
}
