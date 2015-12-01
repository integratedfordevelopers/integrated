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

use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridExtension extends \Twig_Extension
{
    /**
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     */
    public function __construct()
    {
        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults([
            'template' => 'IntegratedWebsiteBundle:Page:grid.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_grid', [$this, 'renderGrid'], ['is_safe' => ['html'], 'needs_environment' => true, 'needs_context' => true]),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param array $context
     * @param string $id
     * @param array $options
     * @return string
     */
    public function renderGrid(\Twig_Environment $environment, $context, $id, array $options = [])
    {
        $options = $this->resolver->resolve($options);

        $edit = isset($context['integrated_block_edit']) && true === $context['integrated_block_edit'];
        $page = isset($context['page']) ? $context['page'] : null;

        $html = '';

        if ($edit) {
            $html .= '<div class="integrated-website-grid">';
        }

        if ($page instanceof Page) {
            $grid = $page->getGrid($id);

            if (!$grid instanceof Grid) {
                $grid = new Grid($id);
            }

            if ($edit) {
                $html .= '<script type="text/json">' . json_encode(['data' => $grid->toArray()]) . '</script>';
            }

            $html .= $environment->render($options['template'], ['grid' => $grid]);
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
        return 'integrated_website_grid';
    }
}
