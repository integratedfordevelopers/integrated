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
    public function execute(BlockInterface $block)
    {
        if (!$block instanceof ContentBlock) {
            return;
        }

        $currentRequest = $this->requestStack->getCurrentRequest();

        if (!$currentRequest instanceof Request) {
            return;
        }

        $request = $currentRequest->duplicate(); // don't change original request

        if ($selection = $block->getSearchSelection()) {
            $request->query->add($selection->getFilters());
        }

        $pagination = $this->provider->execute(
            $request,
            $block->getItemsPerPage(),
            $block->getMaxItems(),
            $block->getSlug() . '-page'
        );

        return $this->render([
            'block'      => $block,
            'pagination' => $pagination,
        ]);
    }
}
