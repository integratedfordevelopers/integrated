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
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\ODM\MongoDB\Id\UuidGenerator;

use Integrated\Bundle\MenuBundle\Document\Menu;
use Integrated\Bundle\MenuBundle\Document\MenuItem;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuController extends Controller
{
    /**
     * @var UuidGenerator
     */
    protected $generator;

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
        $options = isset($data['options']) ? $data['options'] : [];

        if (isset($data['data'])) {
            $menu = $this->getMenuFactory()->fromArray($data['data']);

            $this->generator = new UuidGenerator();

            if ($menu instanceof Menu) {
                $this->prepareItems($menu, $options);
            }
        }

        return [
            'menu'    => $menu,
            'options' => $options,
        ];
    }

    /**
     * @param MenuItem $menu
     * @param array $options
     * @param int $depth
     */
    protected function prepareItems(MenuItem $menu, array $options = [], $depth = 1)
    {
        /** @var MenuItem $child */
        foreach ($menu->getChildren() as $child) {
            $child->setAttributes([
                'class' => 'integrated-website-menu-item',
            ]);

            $child->setLinkAttributes([
                'data-action' => 'integrated-website-menu-item-edit',
                'data-id'     => $child->getId(),
            ]);

            $this->prepareItems($child, $options, $depth + 1);
        }

        if (isset($options['depth']) && $depth <= (int) $options['depth']) {
            $uuid = $this->generator->generateV5($this->generator->generateV4(), uniqid(rand(), true));

            $child = $menu->addChild('+', [
                'uri'        => '#',
                'attributes' => [
                    'class' => 'integrated-website-menu-item',
                ],
                'linkAttributes' => [
                    'data-action' => 'integrated-website-menu-item-add',
                    'data-id'     => $uuid,
                ],
            ]);

            $child->setId($uuid);
        }
    }

    /**
     * @return \Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory
     */
    protected function getMenuFactory()
    {
        return $this->get('integrated_menu.menu.database_menu_factory');
    }
}
