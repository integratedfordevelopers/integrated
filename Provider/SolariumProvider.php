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
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\Paginator;
use Integrated\Bundle\ContentBundle\Document\Block\ContentBlock;

/**
 * @todo provider system (INTEGRATED-431)
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SolariumProvider // @todo interface (INTEGRATED-431)
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var DocumentManager
     */
    private $dm;

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
     * @param DocumentManager $dm
     * @param Paginator $paginator
     */
    public function __construct(Client $client, DocumentManager $dm, Paginator $paginator)
    {
        $this->client = $client;
        $this->dm = $dm;
        $this->paginator = $paginator;
    }

    /**
     * @param ContentBlock $block
     * @param Request $request
     * @param array $options
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function execute(ContentBlock $block, Request $request, array $options = [])
    {
        $pageParam = (null !== $block->getId() ? $block->getId() . '-' : '') . 'page';
        $page = (int) $request->query->get($pageParam);
        $exclude = isset($options['exclude']) ? (bool) $options['exclude'] : true;

        if ($page < 1) {
            $page = 1;
        }

        // @todo max page (INTEGRATED-431)

        $pagination = $this->paginator->paginate(
            [
                $this->client,
                $this->getQuery($request, $block->getId(), $block->getFacetFields(), $options),
            ],
            $page,
            $block->getItemsPerPage(),
            [
                'pageParameterName' => $pageParam,
                'maxItems' => $block->getMaxItems(),
            ]
        );

        if (true === $exclude) {
            /** @var \Solarium\QueryType\Select\Result\Document $document */
            foreach ($pagination as $document) {
                $this->registry[$document->offsetGet('type_id')] = true; // exclude already shown items
            }
        }

        return $pagination;
    }

    /**
     * @param Request $request
     * @param string $blockId
     * @param array $facetFields
     * @param array $options
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    protected function getQuery(Request $request, $blockId, array $facetFields = [], array $options = [])
    {
        // @todo cleanup (copied from ContentController)

        $applyExcludes = true;

        $query = $this->client->createSelect();

        $channel = $request->attributes->get('_channel');

        if (!$channel) {
            throw new \RuntimeException('Channel is required'); // @todo improve (INTEGRATED-431)
        }

        $query
            ->createFilterQuery('pub')
            ->setQuery('pub_active: 1 AND pub_time:[* TO NOW] AND pub_end:[NOW TO *] AND facet_channels: ("%1%")', [$channel]);

        if ($search = $request->query->get($blockId . '-search')) {
            $edismax = $query->getEDisMax();
            $edismax->setQueryFields('title^200 content subtitle intro');
            $edismax->setMinimumMatch('75%');

            $query->setQuery($search);

            //I would be strange to exclude items when when a seach text is entered
            $applyExcludes = false;
        }

        $helper = $query->getHelper();
        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        //@todo: provide facet filters from service
        $contentTypes = $request->query->get('contenttypes');

        if (count($contentTypes) && !in_array('type_name', $facetFields)) {
            $facetFields[] = 'type_name';
            $request->query->set('type_name', $contentTypes); // @hack
        }

        $properties = $request->query->get('properties');

        if (count($properties) && !in_array('facet_properties', $facetFields)) {
            $facetFields[] = 'facet_properties';
            $request->query->set('facet_properties', $properties); // @hack
        }

        foreach ($this->dm->getRepository('Integrated\Bundle\ContentBundle\Document\Relation\Relation')->findAll() as $relation) {
            $name = preg_replace("/[^a-zA-Z]/","",$relation->getName());

            $filters = $request->query->get($name);
            if (count($filters)) {
                if (!in_array('facet_' . $relation->getId(), $facetFields)) {
                    $facetFields[] = 'facet_' . $relation->getId();
                }

                $request->query->set('facet_' . $relation->getId(), $filters); // @hack
            }
        }

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

        if (isset($options['filters'])) {
            foreach ((array) $options['filters'] as $field => $value) {
                $query
                    ->createFilterQuery('filter_' . $field)
                    ->setQuery($field . ': (%1%)', array_map($filter, [$value]))
                    ->addTag('filter_' . $field);
            }
        }

        if (count($this->registry) && $applyExcludes) {
            // exclude items
            $query->setQuery($query->getQuery() . ' AND -type_id: (%1%)', [implode(' OR ', array_map($filter, array_keys($this->registry)))]);
        }

        // sorting
        $sortDefault = 'changed';
        $sortOptions = [
            'rel'     => ['name' => 'rel', 'field' => 'score', 'label' => 'relevance', 'order' => 'desc'],
            'changed' => ['name' => 'changed', 'field' => 'pub_edited', 'label' => 'date modified', 'order' => 'desc'],
            'created' => ['name' => 'created', 'field' => 'pub_created', 'label' => 'date created', 'order' => 'desc'],
            'time'    => ['name' => 'time', 'field' => 'pub_time', 'label' => 'publication date', 'order' => 'desc'],
            'title'   => ['name' => 'title', 'field' => 'title_sort', 'label' => 'title', 'order' => 'asc'],
            'random'  => ['name' => 'random', 'field' => 'random_' . mt_rand(), 'label' => 'random', 'order' => 'desc'],
        ];
        $orderOptions = [
            'asc' => 'asc',
            'desc' => 'desc'
        ];

        $sort = $request->query->get('sort', $sortDefault);

        if (strpos($sort, 'custom:') === 0) {
            //support for custom query in database, while waiting for a better solution
            $query->addParam('sort', substr($sort, 7));

        } else {
            $sort = trim(strtolower($sort));
            $sort = array_key_exists($sort, $sortOptions) ? $sort : $sortDefault;

            $query->addSort($sortOptions[$sort]['field'], in_array($request->query->get('order'), $orderOptions) ? $request->query->get('order') : $sortOptions[$sort]['order']);
        }

        return $query;
    }
}
