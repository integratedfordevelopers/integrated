<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return ItemInterface
     */
    public function build()
    {
        // Todo multiple menu's + menu from config?
        $menu = $this->factory->createItem('root');

        // Dispatch configure event
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($this->factory, $menu));

        return $menu;
    }
}