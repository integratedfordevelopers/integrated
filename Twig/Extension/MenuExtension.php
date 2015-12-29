<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Twig\Extension;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ODM\MongoDB\Id\UuidGenerator;

use Knp\Menu\Twig\Helper;

use Integrated\Bundle\MenuBundle\Provider\DatabaseMenuProvider;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;
use Integrated\Bundle\MenuBundle\Document\Menu;
use Integrated\Bundle\MenuBundle\Document\MenuItem;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var DatabaseMenuProvider
     */
    protected $provider;

    /**
     * @var DatabaseMenuFactory
     */
    protected $factory;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     * @var UuidGenerator
     */
    protected $generator;

    /**
     * @param DatabaseMenuProvider $provider
     * @param DatabaseMenuFactory $factory
     * @param Helper $helper
     * @param string $template
     */
    public function __construct(DatabaseMenuProvider $provider, DatabaseMenuFactory $factory, Helper $helper, $template)
    {
        $this->provider = $provider;
        $this->factory = $factory;
        $this->helper = $helper;

        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'depth' => 1,
            'style' => 'tabs',
            'template' => $template,
        ]);

        $this->generator = new UuidGenerator();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'integrated_menu',
                [$this, 'renderMenu'],
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
            new \Twig_SimpleFunction('integrated_menu_prepare', [$this, 'prepareMenu'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param array $context
     * @param string $name
     * @param array $options
     *
     * @return string
     */
    public function renderMenu($context, $name, array $options = [])
    {
        $options = $this->resolver->resolve($options);

        $edit = isset($context['edit']) && true === $context['edit'];
        $menu = $this->provider->get($name);

        $html = '';

        if ($edit) {
            $html .= '<div class="integrated-website-menu">';

            if (!$this->provider->has($name)) {
                $menu = $this->factory->createItem($name);
            }

            $html .= '<script type="text/json">' . json_encode(
                ['data' => $menu->toArray(), 'options' => $options]
            ) . '</script>';
        }

        if ($menu) {
            $html .= $this->helper->render($menu, $options);
        }

        if ($edit) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Menu $menu
     * @param array $options
     * @return string
     */
    public function prepareMenu(Menu $menu = null, array $options = [])
    {
        $html = '';

        if ($menu) {
            $this->prepareItems($menu, $options);

            $html = $this->helper->render($menu, $options);
        }

        return $html;
    }

    /**
     * @param MenuItem $menu
     * @param array $options
     * @param int $depth
     */
    protected function prepareItems(MenuItem $menu, array $options = [], $depth = 1)
    {
        $menu->setChildrenAttribute('class', 'integrated-website-menu-list');

        if (1 === $depth) {
            $menu->setChildrenAttribute('data-json', json_encode($menu->toArray(false)));
        }

        /** @var MenuItem $child */
        foreach ($menu->getChildren() as $child) {
            $child->setAttributes([
                'class'       => 'integrated-website-menu-item',
                'data-action' => 'integrated-website-menu-item-edit',
                'data-json'   => json_encode($child->toArray(false)),
            ]);

            $this->prepareItems($child, $options, $depth + 1); // recursion
        }

        if (isset($options['depth']) && $depth <= (int) $options['depth']) {
            $uuid = $this->generator->generateV5($this->generator->generateV4(), uniqid(rand(), true));

            $child = $menu->addChild('+', [
                'uri'        => '#',
                'attributes' => [
                    'class'       => 'integrated-website-menu-item',
                    'data-action' => 'integrated-website-menu-item-add',
                ],
            ]);

            $child->setId($uuid);
            $child->setAttribute('data-json', json_encode($child->toArray(false)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_website_menu';
    }
}
