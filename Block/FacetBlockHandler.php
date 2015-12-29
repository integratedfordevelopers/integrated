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

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\ContentBundle\Document\Block\FacetBlock;
use Integrated\Bundle\ContentBundle\Document\Block\ContentBlock;
use Integrated\Bundle\ContentBundle\Provider\SolariumProvider;

use Solarium\QueryType\Select\Result\Result;

/**
 * Facet block handler
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FacetBlockHandler extends BlockHandler
{
    /**
     * @var SolariumProvider
     */
    private $blockRegistry;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param BlockHandlerRegistryInterface $blockRegistry
     * @param RequestStack $requestStack
     */
    public function __construct(BlockHandlerRegistryInterface $blockRegistry, RequestStack $requestStack)
    {
        $this->blockRegistry = $blockRegistry;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block)
    {
        if (!$block instanceof FacetBlock) {
            return;
        }

        $contentBlock = $block->getBlock();

        if (!$contentBlock instanceof ContentBlock) {
            return;
        }

        $handler = $this->blockRegistry->getHandler($contentBlock->getType());

        if (!$handler instanceof ContentBlockHandler) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $pagination = $handler->getPagination($contentBlock, $request);

        $result = $pagination->getCustomParameter('result');

        if (!$result instanceof Result) {
            return;
        }

        $facetSet = $result->getFacetSet();

        if (null === $facetSet) {
            return;
        }

        $facets = [];
        foreach ($block->getFields() as $field) {
            $facets[$field->getField()] = [
                'name' => $field->getName(),
                'values' => $facetSet->getFacet($field->getField()),
            ];
        }

        if (!count($facets)) {
            return;
        }

        return $this->render([
            'block' => $block,
            'facets' => $facets,
        ]);
    }
}
