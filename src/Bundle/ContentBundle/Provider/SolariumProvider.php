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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Exception;
use Integrated\Bundle\ContentBundle\Document\Block\ContentBlock;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;
use Knp\Component\Pager\PaginatorInterface;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SolariumProvider
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
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param Client             $client
     * @param DocumentManager    $dm
     * @param PaginatorInterface $paginator
     */
    public function __construct(Client $client, DocumentManager $dm, PaginatorInterface $paginator)
    {
        $this->client = $client;
        $this->dm = $dm;
        $this->paginator = $paginator;
    }

    /**
     * @param ContentBlock|SearchSelection $subject
     * @param Request                      $request
     * @param array                        $options
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     *
     * @throws Exception
     */
    public function execute($subject, Request $request, array $options = [])
    {
        if (!$subject instanceof ContentBlock && !$subject instanceof SearchSelection) {
            throw new Exception('subject is not supported. Only ContentBlock and SearchSelection are supported');
        }

        $pageParam = (null !== $subject->getId() ? $subject->getId().'-' : '').'page';
        $page = (int) $request->query->get($pageParam);
        $exclude = isset($options['exclude']) ? (bool) $options['exclude'] : true;

        if ($page < 1) {
            $page = 1;
        }

        $itemsPerPage = $subject instanceof ContentBlock ? $subject->getItemsPerPage() : 1000;
        $maxItems = $subject instanceof ContentBlock ? $subject->getMaxItems() : $options['maxItems'] ?? 1000;

        $pagination = $this->paginator->paginate(
            [
                $this->client,
                $this->getQuery($subject, $request, $options),
            ],
            $page,
            $itemsPerPage,
            [
                'pageParameterName' => $pageParam,
                'maxItems' => $maxItems,
                'sortFieldParameterName' => null,
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
     * @param ContentBlock|SearchSelection $subject
     * @param Request                      $request
     * @param array                        $options
     *
     * @return Query
     */
    protected function getQuery($subject, Request $request, array $options = [])
    {
        // @todo: provide facet filters from service
        // @todo cleanup (INTEGRATED-431)

        if (!$channel = $request->attributes->get('_channel')) {
            throw new \RuntimeException('Channel is required'); // @todo improve (INTEGRATED-431)
        }

        $query = $this->client->createSelect();
        $query
            ->createFilterQuery('pub')
            ->setQuery('pub_active: 1 AND pub_time:[* TO NOW] AND pub_end:[NOW TO *] AND facet_channels: ("%1%")', [$channel]);

        if ($search = $request->query->get($subject->getId().'-search')) {
            $edismax = $query->getEDisMax();
            $edismax->setQueryFields('title^200 content subtitle intro');
            $edismax->setMinimumMatch('75%');

            $query->setQuery($search);

            // It would be strange to exclude items when a search text is entered
            $options['exclude'] = false;
            $options['sort_default'] = 'rel';
        }

        try {
            $selection = $subject instanceof SearchSelection ? $subject : $subject->getSearchSelection();

            if ($selection) {
                $this->addFacetFilters($query, $subject, (array) $selection->getFilters(), array_merge($options, ['search_selection' => true]));

                if (\is_array($selection->getInternalParams())) {
                    foreach ($selection->getInternalParams() as $key => $value) {
                        $query->addParam($key, $value);
                    }
                }
            }
        } catch (DocumentNotFoundException $e) {
            // search selection is removed
        }

        $count = $this->addFacetFilters($query, $subject, (array) $request->query->all(), $options);

        if (\count($this->registry) && isset($options['exclude']) && true === $options['exclude']) {
            $helper = $query->getHelper();
            $filter = function ($param) use ($helper) {
                return $helper->escapePhrase($param);
            };

            if (0 === $count) {
                // only exclude without facet filtering
                $query->setQuery($query->getQuery().' AND -type_id: (%1%)', [implode(' OR ', array_map($filter, array_keys($this->registry)))]);
            }
        }

        return $query;
    }

    /**
     * @param Query                        $query
     * @param ContentBlock|SearchSelection $subject
     * @param array                        $request
     * @param array                        $options
     *
     * @return int
     */
    protected function addFacetFilters(Query $query, $subject, array $request = [], array $options = [])
    {
        $count = 0;

        $suffix = isset($options['search_selection']) && true === $options['search_selection'] ? '_search_selection' : null;
        $facetFields = $subject instanceof ContentBlock ? $subject->getFacetFields() : [];

        $contentTypes = isset($request['contenttypes']) ? $request['contenttypes'] : [];

        if (\count($contentTypes) && !\in_array('type_name', $facetFields)) {
            $facetFields[] = 'type_name';
            $request['type_name'] = $contentTypes; // @hack
        }

        $properties = isset($request['properties']) ? $request['properties'] : [];

        if (\count($properties) && !\in_array('facet_properties', $facetFields)) {
            $facetFields[] = 'facet_properties';
            $request['facet_properties'] = $properties; // @hack
        }

        foreach ($this->dm->getRepository(Relation::class)->findAll() as $relation) {
            $name = preg_replace('/[^a-zA-Z]/', '', $relation->getName());
            $filters = isset($request[$name]) ? $request[$name] : [];

            if (\count($filters)) {
                if (!\in_array('facet_'.$relation->getId(), $facetFields)) {
                    $facetFields[] = 'facet_'.$relation->getId();
                }

                $request['facet_'.$relation->getId()] = $filters; // @hack
            }
        }

        $helper = $query->getHelper();
        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        if (\count($facetFields)) {
            $facetSet = $query->getFacetSet();

            foreach ($facetFields as $field) {
                $facet = $facetSet
                    ->createFacetField($field.$suffix)
                    ->setMinCount(1)
                    ->setField($field);

                if (null === $suffix) {
                    $facet->getLocalParameters()->setExclude($field);
                }

                $param = isset($request[$field]) ? $request[$field] : null;

                if ($param) {
                    ++$count; // facet fields count

                    $query
                        ->createFilterQuery($field.$suffix)
                        ->setQuery($field.': (%1%)', [implode(' OR ', array_map($filter, $param))])
                        ->addTag($field.$suffix);
                }
            }
        }

        if (isset($options['filters'])) {
            foreach ((array) $options['filters'] as $field => $value) {
                $query
                    ->createFilterQuery('filter_'.$field.$suffix)
                    ->setQuery($field.': (%1%)', array_map($filter, [$value]))
                    ->addTag('filter_'.$field.$suffix);
            }
        }

        $sort = isset($request['sort']) ? $request['sort'] : null;

        if (null !== $suffix || null !== $sort) {
            // always add default sorting with search selections
            $sortDefault = $options['sort_default'] ?? 'changed';
            $sortOptions = $this->getSortOptions();

            $order = isset($request['order']) ? $request['order'] : null;
            $orderOptions = [
                'asc' => 'asc',
                'desc' => 'desc',
            ];

            if (strpos($sort, 'custom:') === 0) {
                // support for custom query in database, while waiting for a better solution
                $query->addParam('sort', substr($sort, 7));
            } else {
                $sort = trim(strtolower($sort));
                $sort = \array_key_exists($sort, $sortOptions) ? $sort : $sortDefault;

                $query->addSort($sortOptions[$sort]['field'], \in_array($order, $orderOptions) ? $order : $sortOptions[$sort]['order']);
            }
        }

        return $count;
    }

    /**
     * @return array
     */
    protected function getSortOptions()
    {
        return [
            'rel' => ['name' => 'rel', 'field' => 'score', 'label' => 'relevance', 'order' => 'desc'],
            'changed' => ['name' => 'changed', 'field' => 'pub_edited', 'label' => 'date modified', 'order' => 'desc'],
            'created' => ['name' => 'created', 'field' => 'pub_created', 'label' => 'date created', 'order' => 'desc'],
            'time' => ['name' => 'time', 'field' => 'pub_time', 'label' => 'publication date', 'order' => 'desc'],
            'title' => ['name' => 'title', 'field' => 'title_sort', 'label' => 'title', 'order' => 'asc'],
            'rank' => ['name' => 'rank', 'field' => 'rank', 'label' => 'rank', 'order' => 'asc'],
            'random' => ['name' => 'random', 'field' => 'random_'.mt_rand(), 'label' => 'random', 'order' => 'desc'],
        ];
    }
}
