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

use Solarium\Client;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentBlockHandler extends BlockHandler
{
    /**
     * @var Client
     */
    private $solr;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param Client $solr
     * @param RequestStack $requestStack
     */
    public function __construct(Client $solr, RequestStack $requestStack)
    {
        $this->solr = $solr;
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

        $request = $this->requestStack->getCurrentRequest()->duplicate(); // don't change original request

        if ($selection = $block->getSearchSelection()) {
            $request->query->add($selection->getFilters());
        }

        $query = $this->getQuery($request);

        return $this->render([
            'block'     => $block,
            'documents' => $this->solr->execute($query),
        ]);
    }

    /**
     * @todo create class (copied from ContentController)
     * @param Request $request
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function getQuery(Request $request)
    {
        $query = $this->solr->createSelect();

        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField('contenttypes')->setField('type_name')->addExclude('contenttypes');
        $facetSet->createFacetField('channels')->setField('facet_channels');

        // TODO this code should be somewhere else
        $relation = $request->query->get('relation');
        if (null !== $relation) {

            $contentType = array();

            /** @var $type \Integrated\Common\ContentType\ContentTypeInterface */
            foreach ($this->get('integrated.form.resolver')->getTypes() as $type) {
                foreach ($type->getRelations() as $typeRelation) {
                    if ($typeRelation->getId() == $relation) {
                        foreach ($typeRelation->getContentTypes() as $relationContentType) {
                            $contentType[] = $relationContentType->getType();
                        }
                        break;
                    }
                }
            }

        } else {
            $contentType = $request->query->get('contenttypes');
        }

        if (is_array($contentType)) {

            if (count($contentType)) {
                $helper = $query->getHelper();
                $filter = function($param) use($helper) {
                    return $helper->escapePhrase($param);
                };

                $query
                    ->createFilterQuery('contenttypes')
                    ->addTag('contenttypes')
                    ->setQuery('type_name: ((%1%))', [implode(') OR (', array_map($filter, $contentType))]);
            }
        }

        // TODO this should be somewhere else:
        $activeChannels = $request->query->get('channels');
        if (is_array($activeChannels)) {

            if (count($activeChannels)) {
                $helper = $query->getHelper();
                $filter = function($param) use($helper) {
                    return $helper->escapePhrase($param);
                };

                $query
                    ->createFilterQuery('channels')
                    ->addTag('channels')
                    ->setQuery('facet_channels: ((%1%))', [implode(') OR (', array_map($filter, $activeChannels))]);
            }
        }

        // sorting
        $sort_default = 'changed';
        $sort_options = [
            'rel'     => ['name' => 'rel', 'field' => 'score', 'label' => 'relevance', 'order' => 'desc'],
            'changed' => ['name' => 'changed', 'field' => 'pub_edited', 'label' => 'date modified', 'order' => 'desc'],
            'created' => ['name' => 'created', 'field' => 'pub_created', 'label' => 'date created', 'order' => 'desc'],
            'time'    => ['name' => 'time', 'field' => 'pub_time', 'label' => 'publication date', 'order' => 'desc'],
            'title'   => ['name' => 'title', 'field' => 'title_sort', 'label' => 'title', 'order' => 'asc']
        ];
        $order_options = [
            'asc' => 'asc',
            'desc' => 'desc'
        ];

        if ($q = $request->get('q')) {
            $dismax = $query->getDisMax();
            $dismax->setQueryFields('title content');

            $query->setQuery($q);

            $sort_default = 'rel';
        }
        else {
            //relevance only available when sorting on specific query
            unset($sort_options['rel']);
        }

        $sort = $request->query->get('sort', $sort_default);
        $sort = trim(strtolower($sort));
        $sort = array_key_exists($sort, $sort_options) ? $sort : $sort_default;

        $query->addSort($sort_options[$sort]['field'], in_array($request->query->get('order'), $order_options) ? $request->query->get('order') : $sort_options[$sort]['order']);

        return $query;
    }
}
