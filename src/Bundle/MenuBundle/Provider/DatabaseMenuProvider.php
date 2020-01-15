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
use Integrated\Bundle\ContentBundle\Provider\SolariumProvider;
use Integrated\Bundle\MenuBundle\Document\MenuItem;
use Integrated\Bundle\PageBundle\Services\SolrUrlExtractor;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var SolariumProvider
     */
    private $solariumProvider;

    /**
     * @var SolrUrlExtractor
     */
    private $urlExtractor;

    /**
     * @param ChannelContextInterface $channelContext
     * @param DocumentRepository      $repository
     * @param SolariumProvider        $solariumProvider
     * @param solrUrlExtractor        $urlExtractor
     */
    public function __construct(ChannelContextInterface $channelContext, DocumentRepository $repository, SolariumProvider $solariumProvider, SolrUrlExtractor $urlExtractor)
    {
        $this->channelContext = $channelContext;
        $this->repository = $repository;
        $this->solariumProvider = $solariumProvider;
        $this->urlExtractor = $urlExtractor;
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

                if (isset($options['editMode']) && $options['editMode'] === false) {
                    $this->repository->getDocumentManager()->detach($menu);
                    $this->parseSearchSelections($menu);
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

    /**
     * @param ItemInterface $menu
     */
    protected function parseSearchSelections(ItemInterface $menu)
    {
        $factory = new MenuFactory();

        $children = [];
        foreach ($menu->getChildren() as $child) {
            if (!$child instanceof MenuItem || !$child->getTypeLink() == MenuItem::TYPE_LINK_SEARCH_SELECTION) {
                $children[] = $child;
                continue;
            }

            if ($child->getSearchSelection() === null) {
                continue;
            }

            $result = $this->solariumProvider->execute(
                $child->getSearchSelection(),
                new Request([], [], ['_channel' => $this->channelContext->getChannel()->getId()]),
                ['maxItems' => $child->getMaxItems(), 'exclude' => false]
            );
            foreach ($result as $row) {
                $children[] = $factory->createItem($row['title'], ['uri' => $this->urlExtractor->getUrl($row, $this->channelContext->getChannel()->getId())]);
            }
        }
        $menu->setChildren($children);

        foreach ($menu->getChildren() as $child) {
            if ($child->hasChildren()) {
                $this->parseSearchSelections($child);
            }
        }
    }
}
