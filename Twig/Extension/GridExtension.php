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

use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $template = 'IntegratedWebsiteBundle::grid.html.twig';

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @param \Twig_Environment $environment
     */
    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
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
     * @return string
     */
    public function renderGrid($context, $id)
    {
        if (isset($context['page']) && ($page = $context['page']) instanceof Page) {
            /** @var Page $page */
            return $this->environment->render($this->template, ['grid' => $page->getGrid($id)]);
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
