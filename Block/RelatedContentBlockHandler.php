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

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ContentBundle\Document\Block\RelatedContentBlock;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Related content block handler
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RelatedContentBlockHandler extends BlockHandler
{

    /**
     * @var Paginator
     * */
    private $paginator;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param Paginator $paginator
     * @param RequestStack $requestStack
     */
    public function __construct(Paginator $paginator, RequestStack $requestStack, DocumentManager $dm)
    {
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        if (!$block instanceof RelatedContentBlock) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $pagination = $this->getPagination($block, $request);

        if (!count($pagination)) {
            return;
        }

        return $this->render([
            'block'      => $block,
            'pagination' => $pagination,
            'document' => $this->getDocument()
        ]);
    }

    /**
     * @param RelatedContentBlock $block
     * @param Request $request
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination|null
     */
    public function getPagination(RelatedContentBlock $block, Request $request)
    {
        /** @var Article $document */
        $document = $this->getDocument();

        if (!$document instanceof ContentInterface) {
            return;
        }


        $excludeDocument = null;
        if ($block->getTypeBlock() == RelatedContentBlock::SHOW_LINKED) {
            $excludeDocument = $document;
            $document = $document->getReferenceByRelationId($block->getRelation()->getId());
            if (!$document) {
                return;
            }
        }

        $query = $this->dm->getRepository('IntegratedContentBundle:Content\Content')
            ->getUsedBy($document, $block->getRelation(), $excludeDocument);

        $contentTypes = $block->getContentTypes();
        if ($contentTypes) {
            $query->field('contentType')->in($contentTypes);
        }

        if ($block->getSortBy()) {
            $query->sort($block->getSortBy());
        }

        $pageParam = $block->getId() . '-page';

        return $this->paginator->paginate(
            $query,
            $request->query->get($pageParam, 1),
            $block->getItemsPerPage(),
            [
                'pageParameterName' => $pageParam,
                'maxItems' => $block->getMaxItems(),
            ]
        );
    }
}
