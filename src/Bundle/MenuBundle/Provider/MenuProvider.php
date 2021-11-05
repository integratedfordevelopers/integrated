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

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class MenuProvider implements MenuProviderInterface
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
     * @var ItemInterface[]
     */
    protected $menus = [];

    /**
     * @param FactoryInterface         $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        if (!$this->has($name, $options)) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (!isset($this->menus[$name])) {
            $this->menus[$name] = $this->factory->createItem($name);
        }

        $this->eventDispatcher->dispatch(
            new ConfigureMenuEvent($this->factory, $this->menus[$name]),
            ConfigureMenuEvent::CONFIGURE
        );

        return $this->menus[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return strpos($name, 'integrated_') === 0;
    }
}
