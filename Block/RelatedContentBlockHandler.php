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
    public function execute(BlockInterface $block)
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


        if ($block->getTypeBlock() == RelatedContentBlock::TYPE_BLOCK_TWO) {
            if ($references = $document->getReferencesByRelationId($block->getRelation()->getId())) {
                $document = $references->first();
            }
            else {
                return;
            }
        }

        $query = $this->dm->getRepository('IntegratedContentBundle:Content\Content')
            ->getCurrentDocumentLinked($document, $block->getRelation());

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $block->getItemsPerPage()
        );
    }
}