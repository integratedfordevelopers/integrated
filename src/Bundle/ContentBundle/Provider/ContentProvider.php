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
use Integrated\Bundle\WorkflowBundle\Solr\Extension\WorkflowExtension;
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;
use Symfony\Component\HttpFoundation\Request;
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
     * @var WorkflowExtension
     */
    private $workflowExtension;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * ContentProvider constructor.
     *
     * @param Client                $client
     * @param DocumentManager       $dm
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationChecker  $authorizationChecker
     * @param bool                  $workflowExtension
     */
    public function __construct(
        Client $client,
        DocumentManager $dm,
        TokenStorageInterface $tokenStorage,
        AuthorizationChecker $authorizationChecker,
        $workflowExtension = false
    ) {
        $this->client = $client;
        $this->dm = $dm;
        $this->tokenStorage = $tokenStorage;
        $this->workflowExtension = $workflowExtension;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Request $request
     * @param $limit
     *
     * @return array
     */
    public function getContentFromSolr(Request $request, $limit)
    {
        $query = $this->client->createSelect();

        // If the request query contains a relation parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        $relation = $request->query->get('relation');
        if (null !== $relation) {
            $contentType = [];

            /** @var Relation $relation */
            if ($relation = $this->dm->getRepository(Relation::class)->find($relation)) {
                foreach ($relation->getTargets() as $target) {
                    $contentType[] = $target->getId();
                }
            }
        } else {
            $contentType = $request->query->get('contenttypes');
        }

        $helper = $query->getHelper();
        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        // If the request query contains a properties parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        $propertiesfilter = $request->query->get('properties');
        if (\is_array($propertiesfilter)) {
            $query
                ->createFilterQuery('properties')
                ->addTag('properties')
                ->setQuery('facet_properties: ((%1%))', [implode(') OR (', array_map($filter, $propertiesfilter))]);
        }

        /** @var Relation $relation */
        foreach ($this->dm->getRepository(Relation::class)->findAll() as $relation) {
            $name = preg_replace('/[^a-zA-Z]/', '', $relation->getName());
            $facetTitles[$name] = $relation->getName();
            $relationfilter = $request->query->get($name);

            if (\is_array($relationfilter)) {
                $query
                    ->createFilterQuery($name)
                    ->addTag($name)
                    ->setQuery('facet_'.$relation->getId().': ((%1%))', [implode(') OR (', array_map($filter, $relationfilter))]);
            }
        }

        if (\is_array($contentType)) {
            if (\count($contentType)) {
                $query
                    ->createFilterQuery('contenttypes')
                    ->addTag('contenttypes')
                    ->setQuery('type_name: ((%1%))', [implode(') OR (', array_map($filter, $contentType))]);
            }
        }

        // If the workflow bundle is loaded then only display the results that the
        // user has read rights to
        if ($this->workflowExtension) {
            $this->addWorkflowFilter($query);
        }

        $activeChannels = $request->query->get('channels');
        if (\is_array($activeChannels)) {
            if (\count($activeChannels)) {
                $query
                    ->createFilterQuery('channels')
                    ->addTag('channels')
                    ->setQuery('facet_channels: ((%1%))', [implode(') OR (', array_map($filter, $activeChannels))]);
            }
        }

        $activeStates = $request->query->get('workflow_state');
        if (\is_array($activeStates)) {
            if (\count($activeStates)) {
                $query
                    ->createFilterQuery('workflow_state')
                    ->addTag('workflow_state')
                    ->setQuery('facet_workflow_state: ((%1%))', [implode(') OR (', array_map($filter, $activeStates))]);
            }
        }

        $activeAssigned = $request->query->get('workflow_assigned');
        if (\is_array($activeAssigned)) {
            if (\count($activeAssigned)) {
                $query
                    ->createFilterQuery('workflow_assigned')
                    ->addTag('workflow_assigned')
                    ->setQuery('facet_workflow_assigned: ((%1%))', [implode(') OR (', array_map($filter, $activeAssigned))]);
            }
        }

        $activeAuthors = $request->query->get('authors');
        if (\is_array($activeAuthors)) {
            if (\count($activeAuthors)) {
                $query
                    ->createFilterQuery('authors')
                    ->addTag('authors')
                    ->setQuery('facet_authors: ((%1%))', [implode(') OR (', array_map($filter, $activeAuthors))]);
            }
        }

        $hasFields = $request->query->get('hasFields');
        if (\is_array($hasFields)) {
            foreach ($hasFields as $field) {
                $query
                    ->createFilterQuery('hasField_'.$field)
                    ->setQuery($field.':[* TO *]');
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
            'rank' => ['name' => 'rank', 'field' => 'rank', 'label' => 'rank', 'order' => 'asc'],
        ];
        $order_options = [
            'asc' => 'asc',
            'desc' => 'desc',
        ];

        if ($q = $request->get('q')) {
            $edismax = $query->getEDisMax();
            $edismax->setQueryFields('title content');
            $edismax->setMinimumMatch('75%');

            $query->setQuery($q);

            $sort_default = 'rel';
        } else {
            // relevance only available when sorting on specific query
            unset($sort_options['rel']);
        }

        $sort = $request->query->get('sort', $sort_default);
        $sort = trim(strtolower($sort));
        $sort = \array_key_exists($sort, $sort_options) ? $sort : $sort_default;

        $query->addSort($sort_options[$sort]['field'], \in_array($request->query->get('order'), $order_options) ? $request->query->get('order') : $sort_options[$sort]['order']);

        $query->setRows($limit);
        $iterator = $this->client->select($query)->getIterator();
        $contents = [];

        while ($iterator->valid()) {
            $content = $iterator->current();
            if (isset($content['type_id']) && $content = $this->dm->getRepository(Content::class)->find($content['type_id'])) {
                $contents[$content->getId()] = $content;
            }
            $iterator->next();
        }

        return $contents;
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
            // admin is always allowed to do everything
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

        /* @var Person $person */
        if ($person = $user->getRelation()) {
            $fq->setQuery($fq->getQuery().' OR author: %1%*', [$person->getId()]);
        }

        return $fq;
    }
}
