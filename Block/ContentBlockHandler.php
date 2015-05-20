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

use Doctrine\ODM\MongoDB\DocumentNotFoundException;
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
     * @var array
     */
    private $registry = [];

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

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $pagination = $this->getPagination($block, $request);

        if (!count($pagination)) {
            return; // @todo show block in edit mode
        }

        return $this->render([
            'block'      => $block,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param ContentBlock $block
     * @param Request $request
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPagination(ContentBlock $block, Request $request)
    {
        $id = $block->getId();

        if (!isset($this->registry[$id])) {

            $request = $request->duplicate(); // don't change original request

            try {
                if ($selection = $block->getSearchSelection()) {
                    $request->query->add($selection->getFilters());
                }

            } catch (DocumentNotFoundException $e) {
                // search selection is removed
            }

            $pagination = $this->provider->execute(
                $request,
                $block->getId(),
                $block->getItemsPerPage(),
                $block->getMaxItems(),
                $block->getFacetFields()
            );

            $this->registry[$id] = $pagination;
        }

        return $this->registry[$id];
    }
}
