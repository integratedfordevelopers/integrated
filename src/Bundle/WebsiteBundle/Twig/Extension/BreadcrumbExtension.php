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

use Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider;
use Knp\Menu\Twig\Helper;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    /**
     * @var BreadcrumbMenuProvider
     */
    protected $provider;

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
     * @param BreadcrumbMenuProvider $provider
     * @param Helper                 $helper
     * @param string                 $template
     */
    public function __construct(
        BreadcrumbMenuProvider $provider,
        Helper $helper,
        $template
    ) {
        $this->provider = $provider;
        $this->helper = $helper;

        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'depth' => 1,
            'style' => 'tabs',
            'template' => $template,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'integrated_breadcrumb',
                [$this, 'renderBreadcrumb'],
                ['is_safe' => ['html'], 'needs_context' => false]
            ),
        ];
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function renderBreadcrumb(array $options = [])
    {
        $options = $this->resolver->resolve($options);

        $menu = $this->provider->get('breadcrumb');

        $html = '';

        if ($menu) {
            $html .= $this->helper->render($menu, $options);
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_breadcrumb_menu';
    }
}
