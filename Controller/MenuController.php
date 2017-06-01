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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuController extends Controller
{
    /**
     * @Template
     *
     * @param Request $request
     * @return array
     */
    public function renderAction(Request $request)
    {
        $data = (array) json_decode($request->getContent(), true);
        $menu = null;

        if (isset($data['data'])) {
            $menu = $this->getMenuFactory()->fromArray($data['data']);
        }

        return [
            'menu'    => $menu,
            'options' => isset($data['options']) ? $data['options'] : [],
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @return \Integrated\Bundle\MenuBundle\Provider\DatabaseMenuProvider
     */
    protected function getMenuProvider()
    {
        return $this->get('integrated_menu.provider.database_menu_provider');
    }

    /**
     * @return \Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory
     */
    protected function getMenuFactory()
    {
        return $this->get('integrated_menu.menu.database_menu_factory');
    }

    /**
     * @return \Integrated\Common\Content\Channel\ChannelInterface|null
     */
    protected function getChannel()
    {
        return $this->get('channel.context')->getChannel();
    }
}
