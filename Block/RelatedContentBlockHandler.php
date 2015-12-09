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

//use MongoDBODMProxies\__CG__\Integrated\Bundle\ContentBundle\Document\Block\RelatedContentBlock;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\ContentBundle\Document\Block\RelatedContentBlock;
use Integrated\Bundle\ContentBundle\Provider\SolariumProvider;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Related content block handler
 *
 * @author Vasil <developer.optimum@gmail.com>
 */
class RelatedContentBlockHandler extends BlockHandler
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
     * @var DocumentManager
     */
    private $dm;

    /**
     * @param SolariumProvider $provider
     * @param RequestStack $requestStack
     */
    public function __construct(SolariumProvider $provider, RequestStack $requestStack, DocumentManager $dm)
    {
        $this->provider = $provider;
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
     * @param bool $exclude
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPagination(RelatedContentBlock $block, Request $request, $exclude = true)
    {
        $request = $request->duplicate(); // don't change original request

        try {
            /** @var Article $document */
            $document = $this->getDocument();

            if (!$document instanceof Article) {
                return;
            }

            $relationId = $block->getRelation()->getId();
            $articleId = $document->getId();

            if ($block->getTypeBlock() == RelatedContentBlock::TYPE_BLOCK_2) {
                $references = $document->getReferencesByRelationId($relationId);

                if (count($references) > 0) {
                    /** @var Article $firstReference */
                    $firstReference = array_shift($references);

                    $articleId = $firstReference->getId();
                } else {
                    return;
                }
            }

            $repository = $this->dm->getRepository('IntegratedContentBundle:Content\Article');

            $references = $repository->findBy(
                array(
                    'relations.relationId' => $relationId,
                    'relations.references.$id'=>$articleId,
                )
            );

            $related_articles = [];

            /** @var Article $reference */
            foreach ($references as $reference) {
                if ($reference->getContentType() == 'article') {
                    $related_articles[] = $reference->getContentType().'-'.$reference->getId();
                }
            }

            if (count($related_articles) == 0) {
                return;
            }

            $request->query->add(['id'=>$related_articles]);
        } catch (DocumentNotFoundException $e) {
            // search selection is removed
        }

        $request->query->set('sort', $block->getSortBy());

        return $this->provider->execute(
            $request,
            $block->getId(),
            $block->getItemsPerPage(),
            $block->getMaxItems(),
            ['id'],
            $exclude
        );
    }
}