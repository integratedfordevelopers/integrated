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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Provider implements MenuProviderInterface
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
    protected $menus = array();

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
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        if (!$this->has($name, $options)) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (!isset($this->menus[$name])) {
            $this->menus[$name] = $this->factory->createItem($name);
        }

        $this->eventDispatcher->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($this->factory, $this->menus[$name])
        );

        return $this->menus[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        return (strpos($name, 'integrated_') === 0);
    }
}
