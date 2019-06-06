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
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContentProvider
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * ContentProvider constructor.
     *
     * @param Client $client
     * @param DocumentManager $dm
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationChecker $authorizationChecker
     * @param Paginator $paginator
     */
    public function __construct(
        Client $client,
        DocumentManager $dm,
        TokenStorageInterface $tokenStorage,
        AuthorizationChecker $authorizationChecker,
        Paginator $paginator
    ) {
        $this->client = $client;
        $this->dm = $dm;
        $this->tokenStorage = $tokenStorage;
        $this->paginator = $paginator;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param ParameterBag $filters
     * @param $limit
     *
     * @return array
     */
    public function getContentAsSolariumQuery(ParameterBag $filters, $limit, $includeFacets = false)
    {
        $query = $this->client->createSelect();
        $this->client->getPlugin('postbigrequest');

        if ($includeFacets) {
            $facetSet = $query->getFacetSet();
            $facetSet->setMinCount(1);
            $facetSet->createFacetField('contenttypes')->setField('type_name')->addExclude('contenttypes');
            $facetSet->createFacetField('channels')->setField('facet_channels')->addExclude('channels');

            $facetSet->createFacetField('workflow_state')->setField('facet_workflow_state')->addExclude('workflow_state');

            $facetSet->createFacetField('workflow_assigned')->setField('facet_workflow_assigned')->addExclude('workflow_assigned');

            $facetSet->createFacetField('authors')->setField('facet_authors')->addExclude('authors');

            $facetSet->createFacetField('properties')->setField('facet_properties')->addExclude('properties');
        }

        // If the request query contains a relation parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        $relation = $filters->get('relation');
        $contentType = [];
        if (null !== $relation) {
            /** @var Relation $relation */
            if ($relation = $this->dm->getRepository(Relation::class)->find($relation)) {
                foreach ($relation->getTargets() as $target) {
                    $contentType[] = $target->getType();
                }
            }
        }

        $helper = $query->getHelper();
        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        // If the request query contains a properties parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        $propertiesfilter = $filters->get('properties');
        if (\is_array($propertiesfilter)) {
            $query
                ->createFilterQuery('properties')
                ->addTag('properties')
                ->setQuery('facet_properties: ((%1%))', [implode(') OR (', array_map($filter, $propertiesfilter))]);
        }

        /** @var Relation $relation */
        foreach ($this->dm->getRepository(Relation::class)->findAll() as $relation) {
            $name = preg_replace('/[^a-zA-Z]/', '', $relation->getName());
            $relationfilter = $filters->get($name);

            if (\is_array($relationfilter)) {
                $query
                    ->createFilterQuery($name)
                    ->addTag($name)
                    ->setQuery('facet_'.$relation->getId().': ((%1%))', [implode(') OR (', array_map($filter, $relationfilter))]);
            }
        }

        if (\count($contentType)) {
            $query
                ->createFilterQuery('contenttypes')
                ->addTag('contenttypes')
                ->setQuery('type_name: ((%1%))', [implode(') OR (', array_map($filter, $contentType))]);
        }

        // only display the results that the user has read rights to
        $this->addWorkflowFilter($query);

        $activeChannels = $filters->get('channels');
        if (\is_array($activeChannels)) {
            if (\count($activeChannels)) {
                $query
                    ->createFilterQuery('channels')
                    ->addTag('channels')
                    ->setQuery('facet_channels: ((%1%))', [implode(') OR (', array_map($filter, $activeChannels))]);
            }
        }

        $activeStates = $filters->get('workflow_state');
        if (\is_array($activeStates)) {
            if (\count($activeStates)) {
                $query
                    ->createFilterQuery('workflow_state')
                    ->addTag('workflow_state')
                    ->setQuery('facet_workflow_state: ((%1%))', [implode(') OR (', array_map($filter, $activeStates))]);
            }
        }

        $activeAssigned = $filters->get('workflow_assigned');
        if (\is_array($activeAssigned)) {
            if (\count($activeAssigned)) {
                $query
                    ->createFilterQuery('workflow_assigned')
                    ->addTag('workflow_assigned')
                    ->setQuery('facet_workflow_assigned: ((%1%))', [implode(') OR (', array_map($filter, $activeAssigned))]);
            }
        }

        $activeAuthors = $filters->get('authors');
        if (\is_array($activeAuthors)) {
            if (\count($activeAuthors)) {
                $query
                    ->createFilterQuery('authors')
                    ->addTag('authors')
                    ->setQuery('facet_authors: ((%1%))', [implode(') OR (', array_map($filter, $activeAuthors))]);
            }
        }

        // sorting
        $sort_default = 'changed';
        $sort_options = [
            'rel' => ['name' => 'rel', 'field' => 'score', 'label' => 'relevance', 'order' => 'desc'],
            'changed' => ['name' => 'changed', 'field' => 'pub_edited', 'label' => 'date modified', 'order' => 'desc'],
            'created' => ['name' => 'created', 'field' => 'pub_created', 'label' => 'date created', 'order' => 'desc'],
            'time' => ['name' => 'time', 'field' => 'pub_time', 'label' => 'publication date', 'order' => 'desc'],
            'title' => ['name' => 'title', 'field' => 'title_sort', 'label' => 'title', 'order' => 'asc'],
            'random' => ['name' => 'random', 'field' => 'random_'.mt_rand(), 'label' => 'random', 'order' => 'desc'],
        ];
        $order_options = [
            'asc' => 'asc',
            'desc' => 'desc',
        ];

        if ($q = $filters->get('q')) {
            $edismax = $query->getEDisMax();
            $edismax->setQueryFields('title content');
            $edismax->setMinimumMatch('75%');

            $query->setQuery($q);

            $sort_default = 'rel';
        } else {
            //relevance only available when sorting on specific query
            unset($sort_options['rel']);
        }

        $sort = $filters->get('sort', $sort_default);
        $sort = trim(strtolower($sort));
        $sort = \array_key_exists($sort, $sort_options) ? $sort : $sort_default;

        $query->addSort($sort_options[$sort]['field'], \in_array($filters->get('order'), $order_options) ? $filters->get('order') : $sort_options[$sort]['order']);

        //$query->setRows($limit);

        return $query;
    }


    /**
     * @param ParameterBag $filters
     * @param integer $limit
     * @param boolean $enableFacets
     * @return SlidingPagination
     */
    public function getContentAsPaginator(ParameterBag $filters, int $page, int $limit, bool $enableFacets)
    {
        $query = $this->getContentAsSolariumQuery($filters, $limit, $enableFacets);

        $result = $this->client->select($query);

        $paginator = $this->paginator;
        $paginator = $paginator->paginate(
            [$this->client, $query],
            $page,
            $limit,
            ['sortFieldParameterName' => null]
        );

        return $paginator;
    }

    /**
     * @param ParameterBag $filters
     * @param $limit
     *
     * @return array
     */
    public function getContentAsArray(ParameterBag $filters, $limit)
    {
        $query = $this->getContentAsSolariumQuery($filters, $limit);

        $iterator = $query->getIterator();

        $content = [];

        while ($iterator->valid()) {
            $item = $iterator->current();
            if (isset($item['type_id']) && $item = $this->dm->getRepository(Content::class)->find($item['type_id'])) {
                $content[$item->getId()] = $item;
            }
            $iterator->next();
        }

        return $content;
    }

    /**
     * @param Query $query
     *
     * @return \Solarium\QueryType\Select\Query\FilterQuery
     */
    protected function addWorkflowFilter(Query $query)
    {
        $filterWorkflow = [];

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            //admin is always allowed to do everything
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof GroupableInterface) {
            foreach ($user->getGroups() as $group) {
                $filterWorkflow[] = $group->getId();
            }
        }

        // allow content without workflow
        $fq = $query->createFilterQuery('workflow')
            ->addTag('workflow')
            ->addTag('security')
            ->setQuery('(*:* -security_workflow_read:[* TO *])');

        // allow content with group access
        if ($filterWorkflow) {
            $fq->setQuery($fq->getQuery().' OR (security_workflow_read: ((%1%)) AND security_workflow_write: ((%1%)))', [implode(') OR (', $filterWorkflow)]);
        }

        // always allow access to assinged content
        $fq->setQuery($fq->getQuery().' OR facet_workflow_assigned_id: %1%', [$user->getId()]);

        /* @var Person $person*/
        if ($person = $user->getRelation()) {
            $fq->setQuery($fq->getQuery().' OR author: %1%*', [$person->getId()]);
        }

        return $fq;
    }
}
