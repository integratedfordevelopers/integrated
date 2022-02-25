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

use Doctrine\ODM\MongoDB\Id\UuidGenerator;
use Integrated\Bundle\MenuBundle\Document\Menu;
use Integrated\Bundle\MenuBundle\Document\MenuItem;
use Integrated\Bundle\MenuBundle\Matcher\RecursiveActiveMatcher;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;
use Integrated\Bundle\MenuBundle\Provider\IntegratedMenuProvider;
use Knp\Menu\Twig\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class MenuExtension extends AbstractExtension
{
    /**
     * @var IntegratedMenuProvider
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
     * @var RecursiveActiveMatcher
     */
    private $matcher;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @param IntegratedMenuProvider $provider
     * @param DatabaseMenuFactory    $factory
     * @param Helper                 $helper
     * @param RecursiveActiveMatcher $matcher
     * @param RequestStack           $requestStack
     * @param string                 $template
     */
    public function __construct(
        IntegratedMenuProvider $provider,
        DatabaseMenuFactory $factory,
        Helper $helper,
        RecursiveActiveMatcher $matcher,
        RequestStack $requestStack,
        $template
    ) {
        $this->provider = $provider;
        $this->factory = $factory;
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->request = $requestStack->getMasterRequest();

        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'depth' => 1,
            'style' => 'tabs',
            'template' => $template,
            'editMode' => false,
        ]);

        $this->generator = new UuidGenerator();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'integrated_menu',
                [$this, 'renderMenu'],
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
            new TwigFunction('integrated_menu_prepare', [$this, 'prepareMenu'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param array  $context
     * @param string $name
     * @param array  $options
     *
     * @return string
     */
    public function renderMenu($context, $name, array $options = [])
    {
        $options = $this->resolver->resolve($options);

        $edit = $this->request && $this->request->attributes->get('integrated_menu_edit');
        if ($edit) {
            $options['editMode'] = true;
        }

        if ($this->provider->has($name)) {
            $menu = $this->provider->get($name, $options);
        } else {
            $menu = $this->factory->createItem($name);
        }

        $html = '';

        if ($edit) {
            $html .= '<div class="integrated-website-menu">';

            $html .= '<script type="text/json">'.json_encode(
                ['data' => $menu->toArray(), 'options' => $options]
            ).'</script>';
        }

        if ($menu) {
            $this->matcher->setActive($menu);

            $html .= $this->helper->render($menu, $options);
        }

        if ($edit) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Menu  $menu
     * @param array $options
     *
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
     * @param array    $options
     * @param int      $depth
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
                'class' => 'integrated-website-menu-item',
                'data-action' => 'integrated-website-menu-item-edit',
                'data-json' => json_encode($child->toArray(false)),
            ]);

            $this->prepareItems($child, $options, $depth + 1); // recursion
        }

        if (isset($options['depth']) && $depth <= (int) $options['depth']) {
            $uuid = $this->generator->generateV5($this->generator->generateV4(), uniqid(rand(), true));

            $child = $menu->addChild('+', [
                'uri' => '#',
                'attributes' => [
                    'class' => 'integrated-website-menu-item',
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
