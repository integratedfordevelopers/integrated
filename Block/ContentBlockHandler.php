<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Block;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\ContentBundle\Document\Block\ContentBlock;
use Integrated\Bundle\ContentBundle\Provider\SolariumProvider;

/**
 * Content block handler
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentBlockHandler extends BlockHandler
{
    /**
     * @var SolariumProvider
     */
    private $provider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param SolariumProvider $provider
     * @param RequestStack $requestStack
     */
    public function __construct(SolariumProvider $provider, RequestStack $requestStack)
    {
        $this->provider = $provider;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        if (!$block instanceof ContentBlock) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $pagination = $this->getPagination($block, $request, $options);

        if (!count($pagination)) {
            return;
        }

        return $this->render([
            'block'      => $block,
            'pagination' => $pagination,
            'document'   => $this->getDocument(),
        ]);
    }

    /**
     * @param ContentBlock $block
     * @param Request $request
     * @param array $options
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPagination(ContentBlock $block, Request $request, array $options = [])
    {
        return $this->provider->execute($block, $request->duplicate(), $options); // don't change original request
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'filters' => [],   // add extra filters (overwrites search selection)
            'exclude' => true, // exclude already shown items
        ]);

        $resolver->setAllowedTypes('filters', 'array');
        $resolver->setAllowedTypes('exclude', 'bool');
    }
}
