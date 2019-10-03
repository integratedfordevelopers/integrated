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
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbItem;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;
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
     * @var BreadcrumbResolver
     */
    private $breadcrumbResolver;

    /**
     * @var string
     */
    protected $template;

    /**
     * @param BreadcrumbMenuProvider $provider
     * @param Helper                 $helper
     * @param BreadcrumbResolver     $breadcrumbResolver
     * @param string                 $template
     */
    public function __construct(
        BreadcrumbMenuProvider $provider,
        Helper $helper,
        BreadcrumbResolver $breadcrumbResolver,
        string $template
    ) {
        $this->provider = $provider;
        $this->helper = $helper;
        $this->breadcrumbResolver = $breadcrumbResolver;
        $this->template = $template;
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
            new TwigFunction(
                'integrated_breadcrumb_items',
                [$this, 'getBreadcrumb'],
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
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults([
            'depth' => 1,
            'style' => 'tabs',
            'template' => $this->template,
        ]);
        $options = $optionsResolver->resolve($options);

        $menu = $this->provider->get('breadcrumb');

        $html = '';

        if ($menu) {
            $html .= $this->helper->render($menu, $options);
        }

        return $html;
    }


    /**
     * @return BreadcrumbItem[]
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumbResolver->getBreadcrumb();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_breadcrumb_menu';
    }
}
