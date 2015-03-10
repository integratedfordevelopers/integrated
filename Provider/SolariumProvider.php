<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Provider;

use Symfony\Component\HttpFoundation\Request;

use Solarium\Client;

use Knp\Component\Pager\Paginator;

/**
 * @todo provider system
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SolariumProvider // @todo interface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param Client $client
     */
    public function __construct(Client $client, Paginator $paginator)
    {
        $this->client = $client;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     * @param int $limit
     * @param int $maxItems
     * @param string $parameterName
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function execute(Request $request, $limit = 10, $maxItems = 0, $parameterName = 'page')
    {
        $page = (int) $request->query->get($parameterName);

        if ($page < 1) {
            $page = 1;
        }

        // @todo max page

        $pagination = $this->paginator->paginate(
            [
                $this->client,
                $this->getQuery($request),
            ],
            $page,
            $limit,
            [
                'pageParameterName' => $parameterName,
                'maxItems' => $maxItems,
            ]
        );

        /** @var \Solarium\QueryType\Select\Result\Document $document */
        foreach ($pagination as $document) {
            $this->registry[$document->offsetGet('type_id')] = true; // exclude
        }

        return $pagination;
    }

    /**
     * @param Request $request
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function getQuery(Request $request)
    {
        // TODO cleanup (copied from ContentController)
        // TODO filter by active channel and publication filters

        $query = $this->client->createSelect();

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
                $filter = function ($param) use ($helper) {
                    return $helper->escapePhrase($param);
                };

                $bind = [implode(') OR (', array_map($filter, $contentType))];

                if (count($this->registry)) {
                    $bind[] = implode('") OR ("', array_keys($this->registry));
                }

                $query
                    ->createFilterQuery('contenttypes')
                    ->addTag('contenttypes')
                    ->setQuery('type_name: ((%1%))' . (count($this->registry) ? ' AND -type_id: (("%2%"))' : ''), $bind);
            }
        }

        // TODO this should be somewhere else:
        $activeChannels = $request->query->get('channels');
        if (is_array($activeChannels)) {

            if (count($activeChannels)) {
                $helper = $query->getHelper();
                $filter = function ($param) use ($helper) {
                    return $helper->escapePhrase($param);
                };

                $query
                    ->createFilterQuery('channels')
                    ->addTag('channels')
                    ->setQuery('facet_channels: ((%1%))', [implode(') OR (', array_map($filter, $activeChannels))]);
            }
        }

        // sorting
        $sortDefault = 'changed';
        $sortOptions = [
            'rel'     => ['name' => 'rel', 'field' => 'score', 'label' => 'relevance', 'order' => 'desc'],
            'changed' => ['name' => 'changed', 'field' => 'pub_edited', 'label' => 'date modified', 'order' => 'desc'],
            'created' => ['name' => 'created', 'field' => 'pub_created', 'label' => 'date created', 'order' => 'desc'],
            'time'    => ['name' => 'time', 'field' => 'pub_time', 'label' => 'publication date', 'order' => 'desc'],
            'title'   => ['name' => 'title', 'field' => 'title_sort', 'label' => 'title', 'order' => 'asc']
        ];
        $orderOptions = [
            'asc' => 'asc',
            'desc' => 'desc'
        ];

        if ($q = $request->get('q')) {
            $dismax = $query->getDisMax();
            $dismax->setQueryFields('title content');

            $query->setQuery($q);

            $sortDefault = 'rel';
        } else {
            //relevance only available when sorting on specific query
            unset($sortOptions['rel']);
        }

        $sort = $request->query->get('sort', $sortDefault);
        $sort = trim(strtolower($sort));
        $sort = array_key_exists($sort, $sortOptions) ? $sort : $sortDefault;

        $query->addSort($sortOptions[$sort]['field'], in_array($request->query->get('order'), $orderOptions) ? $request->query->get('order') : $sortOptions[$sort]['order']);

        return $query;
    }
}
