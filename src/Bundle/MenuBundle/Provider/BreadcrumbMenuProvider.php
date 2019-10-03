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

use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class BreadcrumbMenuProvider implements MenuProviderInterface
{
    /**
     * @var FactoryInterface
     */
    protected $menuFactory = null;

    /**
     * @var BreadcrumbResolver
     */
    private $breadcrumbResolver;

    /**
     * @param FactoryInterface   $menuFactory
     * @param BreadcrumbResolver $breadcrumbResolver
     */
    public function __construct(FactoryInterface $menuFactory, BreadcrumbResolver $breadcrumbResolver)
    {
        $this->menuFactory = $menuFactory;
        $this->breadcrumbResolver = $breadcrumbResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = [])
    {
        if ($name !== 'breadcrumb') {
            throw new \Exception('This provider can be used for menu "breadcrumb" only');
        }
        $menu = $this->menuFactory->createItem($name, $options);

        foreach ($this->breadcrumbResolver->getBreadcrumb() as $breadcrumbItem) {
            $menu->addChild(
                $breadcrumbItem->getName(),
                ['uri' => $breadcrumbItem->getUrl()]
            );
        }

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = [])
    {
        return null !== $this->get($name, $options);
    }
}
