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

use Knp\Menu\Twig\Helper;

use Integrated\Bundle\MenuBundle\Provider\DatabaseMenuProvider;
use Integrated\Bundle\MenuBundle\Menu\DatabaseMenuFactory;

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
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     * @param DatabaseMenuProvider $provider
     * @param DatabaseMenuFactory $factory
     * @param Helper $helper
     */
    public function __construct(DatabaseMenuProvider $provider, DatabaseMenuFactory $factory, Helper $helper)
    {
        $this->provider = $provider;
        $this->factory = $factory;
        $this->helper = $helper;

        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'depth' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_menu', [$this, 'renderMenu'], ['is_safe' => ['html'], 'needs_context' => true]),
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

            $html .= '<script type="text/json">' . json_encode(['data' => $menu->toArray(), 'options' => $options]) . '</script>';
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_website_menu';
    }
}
