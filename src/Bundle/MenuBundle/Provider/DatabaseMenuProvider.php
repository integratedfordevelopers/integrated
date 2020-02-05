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

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class DatabaseMenuProvider implements MenuProviderInterface
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var ItemInterface[]
     */
    protected $menus = [];

    /**
     * @param ChannelContextInterface $channelContext
     * @param DocumentRepository      $repository
     */
    public function __construct(ChannelContextInterface $channelContext, DocumentRepository $repository)
    {
        $this->channelContext = $channelContext;
        $this->repository = $repository;
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

        if (!isset($this->menus[$name][$channel])) {
            if ($menu = $this->repository->findOneBy(['name' => $name, 'channel.$id' => $channel])) {
                if ($menu instanceof ItemInterface) {
                    $this->resolveParent($menu);
                }

                $this->menus[$name][$channel] = $menu;
            }
        }

        if (isset($this->menus[$name][$channel])) {
            return $this->menus[$name][$channel];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return null !== $this->get($name, $options);
    }

    /**
     * @param ItemInterface $menu
     */
    protected function resolveParent(ItemInterface $menu)
    {
        foreach ($menu->getChildren() as $child) {
            $child->setParent($menu);

            if ($child->hasChildren()) {
                $this->resolveParent($child); // recursion
            }
        }
    }
}
