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

use Symfony\Bridge\Twig\Form\TwigRendererInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;

use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var TwigRendererInterface
     */
    private $renderer;

    /**
     * @var FormFactory
     */
    private $form;

    /**
     * @param \Twig_Environment $environment
     */
    public function __construct(\Twig_Environment $environment, TwigRendererInterface $renderer, FormFactory $form)
    {
        $this->environment = $environment;
        $this->renderer = $renderer;
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_grid', [$this, 'renderGrid'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * @param array $context
     * @param string $id
     * @param string $template
     * @return string
     */
    public function renderGrid($context, $id, $template = 'IntegratedWebsiteBundle:Page:grid.html.twig')
    {
        if (isset($context['form']) && ($form = $context['form']) instanceof FormView) {
            /** @var FormView $form */

            foreach ($form->offsetGet('grids') as $grid) {

                if ($grid->vars['value'] instanceof Grid && $grid->vars['value']->getId() == $id) {

                    return $this->renderer->searchAndRenderBlock($grid, 'row');
                }
            }

            if (isset($form->vars['value']) && ($page = $form->vars['value']) instanceof Page) {
                /** @var Page $page */

                $grid = new Grid();
                $grid->setId($id);

                $page->addGrid($grid);

                $form = $this->form->create('integrated_page_page', $page)->createView();

                return $this->renderer->searchAndRenderBlock($form->offsetGet('grids')->offsetGet($page->indexOf($grid)), 'row');
            }
        }

        if (isset($context['page']) && ($page = $context['page']) instanceof Page) {
            /** @var Page $page */
            return $this->environment->render($template, ['grid' => $page->getGrid($id)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_website_grid';
    }
}
