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
     * @param array $facetFields
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function execute(Request $request, $limit = 10, $maxItems = 0, $parameterName = 'page', array $facetFields = [])
    {
        $page = (int) $request->query->get($parameterName);

        if ($page < 1) {
            $page = 1;
        }

        // @todo max page

        $pagination = $this->paginator->paginate(
            [
                $this->client,
                $this->getQuery($request, $facetFields),
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
     * @param array $facetFields
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function getQuery(Request $request, array $facetFields = [])
    {
        $query = $this->client->createSelect();

        $channel = $request->attributes->get('_channel');

        // @todo check channel

        $query
            ->createFilterQuery('pub')
            ->setQuery('pub_active: 1 AND pub_time:[* TO NOW] AND pub_end:[NOW TO *] AND facet_channels: ("%1%")', [$channel]);

        $helper = $query->getHelper();

        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        if (count($facetFields)) {

            $facetSet = $query->getFacetSet();

            foreach ($facetFields as $field) {

                $facetSet
                    ->createFacetField($field)
                    ->setMinCount(1)
                    ->setField($field)
                    ->addExclude($field);

                if ($param = $request->query->get($field)) {

                    $query
                        ->createFilterQuery($field)
                        ->setQuery($field . ': (%1%)', [implode(' OR ', array_map($filter, $param))])
                        ->addTag($field);
                }
            }
        }

        if (count($this->registry)) {
            // exclude items
            $query->setQuery($query->getQuery() . ' AND -type_id: (%1%)', [implode(' OR ', array_map($filter, array_keys($this->registry)))]);
        }

        // @todo cleanup (copied from ContentController)

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

        $sort = $request->query->get('sort', $sortDefault);
        $sort = trim(strtolower($sort));
        $sort = array_key_exists($sort, $sortOptions) ? $sort : $sortDefault;

        $query->addSort($sortOptions[$sort]['field'], in_array($request->query->get('order'), $orderOptions) ? $request->query->get('order') : $sortOptions[$sort]['order']);

        return $query;
    }
}
