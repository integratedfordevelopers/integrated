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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ContentBundle\Document\Block\RelatedContentBlock;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Block\BlockInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Related content block handler.
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RelatedContentBlockHandler extends BlockHandler
{
    /**
     * @var Paginator
     */
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
     * @param Paginator       $paginator
     * @param RequestStack    $requestStack
     * @param DocumentManager $dm
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
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return null;
        }

        $pagination = $this->getPagination($block, $request);

        if (null === $pagination || !\count($pagination)) {
            return null;
        }

        return $this->render([
            'block' => $block,
            'pagination' => $pagination,
            'document' => $this->getDocument(),
        ]);
    }

    /**
     * @param RelatedContentBlock $block
     * @param Request             $request
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface|null
     */
    public function getPagination(RelatedContentBlock $block, Request $request)
    {
        $target = $this->getQuery($block);

        if (null === $target) {
            return null;
        }

        $pageParam = $block->getId().'-page';

        return $this->paginator->paginate(
            $target,
            $request->query->get($pageParam, 1),
            $block->getItemsPerPage(),
            [
                'pageParameterName' => $pageParam,
                'maxItems' => $block->getMaxItems(),
            ]
        );
    }

    /**
     * @param RelatedContentBlock $block
     *
     * @return \Doctrine\MongoDB\Query\Builder|\Doctrine\Common\Collections\ArrayCollection|null
     */
    protected function getQuery(RelatedContentBlock $block)
    {
        /** @var Article $document */
        $document = $this->getDocument();

        if (!$document instanceof Content) {
            return null;
        }

        switch ($block->getTypeBlock()) {
            case RelatedContentBlock::SHOW_LINKED_BY:
                $query = $this->getLinkedByQuery($document, $block);

                break;
            case RelatedContentBlock::SHOW_LINKED:
                if (!$linkedDocuments = $document->getReferencesByRelationId($block->getRelation()->getId())) {
                    return null;
                }

                $query = $this->dm->getRepository(Content::class)->getUsedBy($linkedDocuments, $block->getRelation(), $document);

                break;
            default:
                $query = $this->dm->getRepository(Content::class)->getUsedBy(new ArrayCollection([$document]), $block->getRelation());

                break;
        }

        if ($block->getContentTypes()) {
            $query->field('contentType')->in($block->getContentTypes());
        }

        if ($block->getSortBy()) {
            $query->sort($block->getSortBy(), $block->getSortDirection());
        }

        return $query;
    }

    /**
     * @param Content             $document
     * @param RelatedContentBlock $block
     *
     * @return \Doctrine\MongoDB\Query\Builder
     */
    protected function getLinkedByQuery(Content $document, RelatedContentBlock $block)
    {
        $ids = [];

        foreach ($document->getReferencesByRelationId($block->getRelation()->getId()) as $content) {
            $ids[$content->getId()] = $content->getId();
        }

        return $this->dm->getRepository(Content::class)
            ->createQueryBuilder()
            ->field('_id')->in($ids);
    }
}
