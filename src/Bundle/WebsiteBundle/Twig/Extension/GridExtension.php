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

use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var RequestStack|null
     */
    protected $request;

    /**
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMasterRequest();

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
            new \Twig_SimpleFunction(
                'integrated_grid',
                [$this, 'renderGrid'],
                ['is_safe' => ['html'], 'needs_environment' => true, 'needs_context' => true]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param array $context
     * @param string $id
     * @param array $options
     *
     * @return string
     */
    public function renderGrid(\Twig_Environment $environment, $context, $id, array $options = [])
    {
        $options = $this->resolver->resolve($options);

        $page = isset($context['page']) ? $context['page'] : null;

        if ($page instanceof AbstractPage) {
            $grid = $page->getGrid($id);

            if (!$grid instanceof Grid) {
                $grid = new Grid($id);
            }

            return $environment->render($options['template'], [
                'grid' => $grid,
            ]);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_website_grid';
    }
}
