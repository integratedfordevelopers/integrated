<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Image;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\ActionsType;
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Form\ContentFormType;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Locks;
use Integrated\Common\Security\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentController extends Controller
{
    /**
     * @var string
     */
    protected $relationClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation';

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // group the types based on there class
        $types = [];

        // Store contentTypes in array
        $displayTypes = [];

        // Store facetTitles in array
        $facetTitles = [];

        //remember search state
        $session = $request->getSession();
        if ($request->query->get('remember') && $session->has('content_index_view')) {
            $request->query->add(unserialize($session->get('content_index_view')));
            $request->query->remove('remember');
        } elseif (!$request->getRequestFormat() == 'json') {
            $session->set('content_index_view', serialize($request->query->all()));
        }

        /** @var $type \Integrated\Common\ContentType\ContentTypeInterface */
        foreach ($this->get('integrated.form.resolver')->getTypes() as $type) {
            $types[$type->getClass()][$type->getType()] = $type;
            $displayTypes[$type->getType()] = $type->getName();
        }

        foreach (array_keys($types) as $key) {
            ksort($types[$key]);
        }

        /** @var $client \Solarium\Client */
        $client = $this->get('solarium.client');
        $query = $client->createSelect();

        $facetSet = $query->getFacetSet();
        $facetSet->setMinCount(1);
        $facetSet->createFacetField('contenttypes')->setField('type_name')->addExclude('contenttypes');
        $facetSet->createFacetField('channels')->setField('facet_channels')->addExclude('channels');

        $facetSet->createFacetField('workflow_state')->setField('facet_workflow_state')->addExclude('workflow_state');
        $facetTitles['workflow_state'] = 'Workflow status';

        $facetSet->createFacetField('workflow_assigned')->setField('facet_workflow_assigned')->addExclude('workflow_assigned');
        $facetTitles['workflow_assigned'] = 'Assigned user';

        $facetSet->createFacetField('authors')->setField('facet_authors')->addExclude('authors');
        $facetTitles['authors'] = 'Author';

        $facetSet->createFacetField('properties')->setField('facet_properties')->addExclude('properties');

        // If the request query contains a relation parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        // TODO this code should be somewhere else
        $relation = $request->query->get('relation');
        $relations = [];
        if (null !== $relation) {
            $contentType = [];

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();

            /** @var Relation $relation */
            if ($relation = $dm->getRepository($this->relationClass)->find($relation)) {
                foreach ($relation->getTargets() as $target) {
                    $contentType[] = $target->getType();
                    $relations[] = [
                        'href' => $this->generateUrl('integrated_content_content_new', ['class' => $target->getClass(), 'type' => $target->getType(), 'relation' => $relation->getId()]),
                        'name' => $target->getName(),
                    ];
                }
            }
        } else {
            $contentType = $request->query->get('contenttypes');
        }

        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();

        $active = [];

        $helper = $query->getHelper();
        $filter = function ($param) use ($helper) {
            return $helper->escapePhrase($param);
        };

        // If the request query contains a properties parameter we need to fetch all the targets of the relation in order
        // to filter on these targets.
        // TODO this code should be somewhere else
        $propertiesfilter = $request->query->get('properties');
        if (\is_array($propertiesfilter)) {
            $query
                ->createFilterQuery('properties')
                ->addTag('properties')
                ->setQuery('facet_properties: ((%1%))', [implode(') OR (', array_map($filter, $propertiesfilter))]);

            $active['properties'] = $propertiesfilter;
        }

        /** @var Relation $relation */
        foreach ($dm->getRepository($this->relationClass)->findAll() as $relation) {
            $name = preg_replace('/[^a-zA-Z]/', '', $relation->getName());

            //create relation facet field
            $facetSet->createFacetField($name)->setField('facet_'.$relation->getId())->addExclude($name);
            $facetTitles[$name] = $relation->getName();
            $relationfilter = $request->query->get($name);

            if (\is_array($relationfilter)) {
                $query
                    ->createFilterQuery($name)
                    ->addTag($name)
                    ->setQuery('facet_'.$relation->getId().': ((%1%))', [implode(') OR (', array_map($filter, $relationfilter))]);

                $active[$name] = $relationfilter;
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

        if ($this->has('integrated_workflow.solr.workflow.extension')) {
            $filterWorkflow = [];

            $user = $this->getUser();

            if ($user instanceof GroupableInterface) {
                foreach ($user->getGroups() as $group) {
                    $filterWorkflow[] = $group->getId();
                }
            }

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                // allow content without workflow
                $fq = $query->createFilterQuery('workflow')
                    ->addTag('workflow')
                    ->addTag('security')
                    ->setQuery('(*:* -security_workflow_read:[* TO *])');

                // allow content with group access
                if ($filterWorkflow) {
                    $fq->setQuery(
                        $fq->getQuery().' OR security_workflow_read: ((%1%))',
                        [implode(') OR (', $filterWorkflow)]
                    );
                }

                // always allow access to assigned content
                $fq->setQuery(
                    $fq->getQuery().' OR facet_workflow_assigned_id: %1%',
                    [$user->getId()]
                );

                if ($person = $user->getRelation()) {
                    $fq->setQuery(
                        $fq->getQuery().' OR author: %1%*',
                        [$person->getId()]
                    );
                }
            }
        }

        // TODO this should be somewhere else:
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

        if ($request->isMethod('post')) {
            $id = (array) $request->get('id');
            if (\is_array($id)) {
                if (\count($id) == 0) {
                    $id[] = '';
                }

                if (\count($id)) {
                    $query
                        ->createFilterQuery('id')
                        ->addTag('id')
                        ->setQuery('type_id: ((%1%))', [implode(') OR (', array_map($filter, $id))]);
                }
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

        if ($q = $request->get('q')) {
            $edismax = $query->getEDisMax();
            $edismax->setQueryFields('title content');
            $edismax->setMinimumMatch('75%');

            $query->setQuery($q);

            $sort_default = 'rel';
        } else {
            //relevance only available when sorting on specific query
            unset($sort_options['rel']);
        }

        $sort = $request->query->get('sort', $sort_default);
        $sort = trim(strtolower($sort));
        $sort = array_key_exists($sort, $sort_options) ? $sort : $sort_default;

        $query->addSort($sort_options[$sort]['field'], \in_array($request->query->get('order'), $order_options) ? $request->query->get('order') : $sort_options[$sort]['order']);

        // add field filters
        foreach ((array) $request->query->get('filter') as $name => $value) {
            $value = trim($value);
            if (!\is_string($name) || !\is_string($value) || !$value) {
                continue;
            }

            $query->createFilterQuery($name)->setQuery('%1%:%P2%', [$name, $value]);
        }

        // Execute the query
        $result = $client->select($query);

        /** @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        $paginator = $paginator->paginate(
            [$client, $query],
            $request->query->get('page', 1),
            $request->query->get('limit', 15),
            ['sortFieldParameterName' => null]
        );

        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $channels = [];
        if ($channelResult = $dm->getRepository('Integrated\\Bundle\\ContentBundle\\Document\\Channel\\Channel')->findAll()) {
            /** @var $channel \Integrated\Bundle\ContentBundle\Document\Channel\Channel */
            foreach ($channelResult as $channel) {
                $channels[$channel->getId()] = $channel->getName();
            }
        }

        $active['contenttypes'] = $contentType;
        $active['channels'] = $activeChannels;
        $active['workflow_state'] = $activeStates;
        $active['workflow_assigned'] = $activeAssigned;
        $active['authors'] = $activeAuthors;

        return $this->render('IntegratedContentBundle:content:index.'.$request->getRequestFormat().'.twig', [
            'types' => $types,
            'params' => ['sort' => ['current' => $sort, 'default' => $sort_default, 'options' => $sort_options]],
            'pager' => $paginator,
            'contentTypes' => $displayTypes,
            'active' => $active,
            'channels' => $channels,
            'facets' => $result->getFacetSet()->getFacets(),
            'locks' => $this->getLocks($paginator),
            'relations' => $relations,
            'facetTitles' => $facetTitles,
        ]);
    }

    /**
     * Show a document.
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function showAction(Request $request, Content $content)
    {
        return $this->render('IntegratedContentBundle:content:show.'.$request->getRequestFormat().'.twig', [
            'document' => $content,
        ]);
    }

    /**
     * Create a new document.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->get('integrated_content.content_type.manager')->getType($request->get('type'));

        $content = $contentType->create();

        if (!$this->get('security.authorization_checker')->isGranted(Permissions::CREATE, $content)) {
            throw new AccessDeniedException();
        }

        $form = $this->createNewForm($contentType, $content, $request);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->getData() == 'cancel') {
                return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
            }

            if ($form->isValid()) {
                if ($this->has('integrated_solr.indexer')) {
                    //higher priority for content edited in Integrated
                    $subscriber = $this->get('integrated_solr.indexer.mongodb.subscriber');
                    $queue = $subscriber->getQueue();
                    $subscriber->setPriority($queue::PRIORITY_HIGH);
                }

                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->get('doctrine_mongodb')->getManager();

                $dm->persist($content);
                $dm->flush();

                if ($this->has('integrated_solr.indexer')) {
                    $lock = $this->get('integrated_solr.lock.factory')->createLock(self::class);
                    $lock->acquire(true);

                    try {
                        $indexer = $this->get('integrated_solr.indexer');
                        $indexer->setOption('queue.size', 2);
                        $indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
                    } finally {
                        $lock->release();
                    }
                }

                if ($request->getRequestFormat() == 'iframe.html') {
                    return $this->render(
                        'IntegratedContentBundle:content:saved.iframe.html.twig',
                        [
                            'id' => $content->getId(),
                            'title' => method_exists($content, 'getTitle') ? $content->getTitle() : $content->getId(),
                            'relation' => $request->get('relation'),
                        ]
                    );
                }

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success(
                    $this->get('translator')->trans('The document %name% has been created', ['%name%' => $contentType->getName()])
                );

                return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
            }
        }

        return $this->render(sprintf('IntegratedContentBundle:content:new.%s.twig', $request->getRequestFormat()), [
            'editable' => true,
            'type' => $contentType,
            'form' => $form->createView(),
            'hasWorkflowBundle' => $this->has('integrated_workflow.form.workflow.state.type'),
            'hasContentHistoryBundle' => false, // not needed here
            'references' => json_encode($this->getReferences($content)),
        ]);
    }

    /**
     * Update a existing document.
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function editAction(Request $request, Content $content)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->get('integrated_content.content_type.manager')->getType($content->getContentType());

        if (!$this->get('security.authorization_checker')->isGranted(Permissions::VIEW, $content)) {
            throw new AccessDeniedException();
        }

        // get a lock on this content resource.

        $locking = $this->getLock($content, 15);
        $locking['locked'] = $locking['lock'] ? true : false;

        if ($locking['lock'] && $locking['owner']) {
            if ($request->query->has('lock') && $locking['lock']->getId() == $request->query->get('lock')) {
                $locking['locked'] = false;
            }

            if ($locking['new']) {
                if ($request->isMethod('get')) {
                    return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]));
                }

                $locking['locked'] = false;
            }
        }

        $form = $this->createEditForm($contentType, $content, $locking);

        if ($request->isMethod('put')) {
            $form->handleRequest($request);

            // possible actions are cancel, back, reload, reload_changed and save

            if ($form->get('actions')->getData() == 'cancel' || $form->get('actions')->getData() == 'back') {
                if (!$locking['locked']) {
                    $locking['release']();
                }

                $url = $form->get('returnUrl')->getData() ?: $this->generateUrl('integrated_content_content_index', ['remember' => 1]);

                return $this->redirect($url);
            }

            if (!$this->get('security.authorization_checker')->isGranted(Permissions::EDIT, $content)) {
                throw new AccessDeniedException();
            }

            if ($form->get('actions')->getData() == 'reload') {
                return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]));
            }

            // this is not rest compatible since a button click is required to save
            if ($form->get('actions')->getData() == 'save') {
                if (!$locking['locked'] && $form->isValid()) {
                    if ($this->has('integrated_solr.indexer')) {
                        //higher priority for content edited in Integrated
                        $subscriber = $this->get('integrated_solr.indexer.mongodb.subscriber');
                        $queue = $subscriber->getQueue();
                        $subscriber->setPriority($queue::PRIORITY_HIGH);
                    }

                    /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->flush();

                    // Set flash message
                    $this->get('braincrafted_bootstrap.flash')->success(
                        $this->get('translator')->trans('The changes to %name% are saved', ['%name%' => $contentType->getName()])
                    );

                    if ($this->has('integrated_solr.indexer')) {
                        $lock = $this->get('integrated_solr.lock.factory')->createLock(self::class);
                        $lock->acquire(true);

                        try {
                            $indexer = $this->get('integrated_solr.indexer');
                            $indexer->setOption('queue.size', 2);
                            $indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
                        } finally {
                            $lock->release();
                        }
                    }

                    if (!$locking['locked']) {
                        $locking['release']();
                    }

                    return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
                }
            }

            // reload_changed is just submitting without saving so the changes made are
            // not lost and there is a new change to get a lock on the content.
        }

        if ($locking['locked']) {
            // the document is locked so display display a error message explaining that
            // the user can not edit this page will the lock is there.

            if ($locking['owner']) {
                $text = 'The document is currently locked by your self in a different browser or tab and can not be edited until this lock is released.';
            } elseif ($locking['user']) {
                $user = $locking['user']->getUsername();

                // we got a basic user name now try to get a better one

                if (method_exists($locking['user'], 'getRelation')) {
                    if ($relation = $locking['user']->getRelation()) {
                        if (method_exists($relation, '__toString')) {
                            $user = (string) $relation;
                        }
                    }
                }

                $text = sprintf('The document is currently locked by %s, the document can not be edited until this lock is released.', $user);
            } else {
                $text = 'The document is currently locked and can not be edited until this lock is released.';
            }

            $this->get('braincrafted_bootstrap.flash')->error($text);
        }

        return $this->render('IntegratedContentBundle:content:edit.html.twig', [
            'editable' => $this->get('security.authorization_checker')->isGranted(Permissions::EDIT, $content),
            'type' => $contentType,
            'form' => $form->createView(),
            'content' => $content,
            'locking' => $locking,
            'hasWorkflowBundle' => $this->has('integrated_workflow.form.workflow.state.type'),
            'hasContentHistoryBundle' => $this->has('integrated_content_history.controller.content_history'),
            'references' => json_encode($this->getReferences($content)),
        ]);
    }

    /**
     * Delete a document.
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function deleteAction(Request $request, Content $content)
    {
        /** @var $type \Integrated\Common\ContentType\ContentTypeInterface */
        $type = $this->get('integrated.form.resolver')->getType($content->getContentType());

        if (!$this->get('security.authorization_checker')->isGranted(Permissions::DELETE, $content)) {
            throw new AccessDeniedException();
        }

        // get a lock on this content resource.

        $locking = $this->getLock($content, 15);
        $locking['locked'] = $locking['lock'] ? true : false;

        if ($locking['lock'] && $locking['owner']) {
            if ($request->query->has('lock') && $locking['lock']->getId() == $request->query->get('lock')) {
                $locking['locked'] = false;
            }

            if ($locking['new']) {
                if ($request->isMethod('get')) {
                    return $this->redirect($this->generateUrl('integrated_content_content_delete', ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]));
                }

                $locking['locked'] = false;
            }
        }

        $contentReferenced = $this->get('integrated_content.services.search.content.referenced');
        $referenced = $contentReferenced->getReferenced($content);

        $form = $this->createDeleteForm($content, $locking, \count($referenced) > 0);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // possible actions are cancel, reload and delete

            if ($form->get('actions')->getData() == 'cancel') {
                if (!$locking['locked']) {
                    $locking['release']();
                }

                return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
            }

            if ($form->get('actions')->getData() == 'reload') {
                return $this->redirect($this->generateUrl('integrated_content_content_delete', ['id' => $content->getId()]));
            }

            // this is not rest compatible since a button click is required to save
            if ($form->get('actions')->getData() == 'delete') {
                if ($form->isValid()) {
                    if ($this->has('integrated_solr.indexer')) {
                        //higher priority for content edited in Integrated
                        $subscriber = $this->get('integrated_solr.indexer.mongodb.subscriber');
                        $queue = $subscriber->getQueue();
                        $subscriber->setPriority($queue::PRIORITY_HIGH);
                    }

                    /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                    $dm = $this->get('doctrine_mongodb')->getManager();

                    $dm->remove($content);
                    $dm->flush();

                    // Set flash message
                    $this->get('braincrafted_bootstrap.flash')->success(
                        $this->get('translator')->trans('The document %name% has been deleted', ['%name%' => $type->getName()])
                    );

                    if ($this->has('integrated_solr.indexer')) {
                        $indexer = $this->get('integrated_solr.indexer');
                        $indexer->setOption('queue.size', 2);
                        $indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
                    }

                    if (!$locking['locked']) {
                        $locking['release']();
                    }

                    return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
                }
            }
        }

        if ($locking['locked']) {
            // the document is locked so display display a error message explaining that
            // the user can not edit this page will the lock is there.

            if ($locking['owner']) {
                $text = 'The document is currently locked by your self in a different browser or tab and can not be deleted until this lock is released.';
            } elseif ($locking['user']) {
                $user = $locking['user']->getUsername();

                // we got a basic user name now try to get a better one

                if (method_exists($locking['user'], 'getRelation')) {
                    if ($relation = $locking['user']->getRelation()) {
                        if (method_exists($relation, '__toString')) {
                            $user = (string) $relation;
                        }
                    }
                }

                $text = sprintf('The document is currently locked by %s, the document can not be deleted until this lock is released.', $user);
            } else {
                $text = 'The document is currently locked and can not be deleted until this lock is released.';
            }

            $this->get('braincrafted_bootstrap.flash')->error($text);
        }

        return $this->render('IntegratedContentBundle:content:delete.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'content' => $content,
            'locking' => $locking,
            'referenced' => $referenced,
        ]);
    }

    /**
     * Get a lock or find out who does have the lock.
     *
     * The result is a array with the following keys:
     * - lock: this will contain the instance of the lock object or null.
     * - user: this is the user the lock belongs to or null if the lock does
     *         not have a owner.
     *
     * @param object     $object
     * @param int | null $timeout
     *
     * @return array
     */
    protected function getLock($object, $timeout = null)
    {
        if (!$this->has('integrated_locking.dbal.manager') || !$this->get('security.authorization_checker')->isGranted(Permissions::EDIT, $object)) {
            return [
                'lock' => null,
                'user' => null,
                'owner' => false,
                'new' => false,
                'release' => function () {
                },
            ];
        }

        /** @var Locks\ManagerInterface $service */
        $service = $this->get('integrated_locking.dbal.manager');

        // Remove expired locks
        $service->clean();

        $object = Locks\Resource::fromObject($object);
        $owner = null;

        if ($user = $this->getUser()) {
            $owner = Locks\Resource::fromAccount($user);
        }

        if ($owner) {
            $request = new Locks\Request($object);
            $request->setOwner($owner);
            $request->setTimeout($timeout);

            if ($lock = $service->acquire($request)) {
                return [
                    'lock' => $lock,
                    'user' => $this->getUser(),
                    'owner' => true,
                    'new' => true,
                    'release' => function () use ($service, $lock) {
                        $service->release($lock);
                    },
                ];
            }
        } // can not acquire a lock if not logged in.

        if ($lock = $service->findByResource($object)) {
            $lock = $lock[0];

            if ($owner && $owner->equals($lock->getRequest()->getOwner())) {
                return [
                    'lock' => $lock,
                    'user' => $this->getUser(),
                    'owner' => true,
                    'new' => false,
                    'release' => function () use ($service, $lock) {
                        $service->release($lock);
                    },
                ];
            }

            // get the user the locks belongs to.
            $user = null;

            if ($owner = $lock->getRequest()->getOwner()) {
                if ($this->has('integrated_user.user.manager')) {
                    /** @var UserManagerInterface $manager */
                    $manager = $this->get('integrated_user.user.manager');

                    if ($manager->getClassName() === $owner->getType()) {
                        $user = $manager->findByUsername($owner->getIdentifier());
                    }
                }
            }

            return [
                'lock' => $lock,
                'user' => $user,
                'owner' => false,
                'new' => false,
                'release' => function () use ($service, $lock) {
                    $service->release($lock);
                },
            ];
        }

        return [
            'lock' => null,
            'user' => null,
            'owner' => false,
            'new' => false,
            'release' => function () {
            },
        ];
    }

    /**
     * @param Traversable $iterator
     *
     * @return array
     */
    protected function getLocks(Traversable $iterator)
    {
        $results = [];

        if (!$this->has('integrated_locking.dbal.manager')) {
            return $results;
        }

        $filter = new Locks\Filter();

        foreach ($iterator as $data) {
            $filter->resources[] = new Locks\Resource($data['type_class'], $data['type_id']);
        }

        if (!$filter->resources) {
            return $results;
        }

        /** @var Locks\ManagerInterface $service */
        $service = $this->get('integrated_locking.dbal.manager');

        foreach ($service->findBy($filter) as $lock) {
            // get the user the locks belongs to.
            $user = null;

            if ($owner = $lock->getRequest()->getOwner()) {
                if ($this->has('integrated_user.user.manager')) {
                    /** @var UserManagerInterface $manager */
                    $manager = $this->get('integrated_user.user.manager');

                    if ($manager->getClassName() === $owner->getType()) {
                        $user = $manager->findByUsername($owner->getIdentifier());
                    }
                }
            }

            $text = '';

            if ($user) {
                $text = $user->getUsername();

                // we got a basic user name now try to get a better one

                if (method_exists($user, 'getRelation')) {
                    if ($relation = $user->getRelation()) {
                        if (method_exists($relation, '__toString')) {
                            $text = (string) $relation;
                        }
                    }
                }
            }

            $results[$lock->getRequest()->getResource()->getIdentifier()] = [
                'lock' => $lock,
                'user' => $text,
            ];
        }

        return $results;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function navdropdownsAction(Request $request)
    {
        $session = $request->getSession();

        $queuecount = (int) $this->container->get('integrated_queue.dbal.provider')->count();
        $queuepercentage = 100;
        if ($queuecount > 0) {
            $queuemaxcount = max($queuecount, $session->get('queuemaxcount'));
            $session->set('queuemaxcount', $queuemaxcount);
            $queuepercentage = round(($queuemaxcount - $queuecount) / $queuemaxcount * 100);
        } else {
            $session->remove('queuemaxcount');
        }

        $email = '';

        $avatarurl = '//www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?s=45';

        /** @var $client \Solarium\Client */
        //
        // Get documents assigned to this user
        //
        $client = $this->get('solarium.client');
        $query = $client->createSelect();

        $assignedContent = [];

        if ($user = $this->getUser()) {
            $userId = $user->getId();

            $query
                ->createFilterQuery('workflow_assigned_id')
                ->setQuery('facet_workflow_assigned_id:'.$userId.'');

            $result = $client->select($query);

            $assignedContent = $result->getDocuments();
        }

        return $this->render('IntegratedContentBundle:content:navdropdowns.html.twig', [
            'avatarurl' => $avatarurl,
            'queuecount' => $queuecount,
            'queuepercentage' => $queuepercentage,
            'assignedContent' => $assignedContent,
        ]);
    }

    /**
     * @param Content $content
     * @param Request $request
     *
     * @return Response
     */
    public function usedByAction(Content $content, Request $request)
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();

        $qb = $dm->createQueryBuilder('IntegratedContentBundle:Content\Content');
        $qb->field('relations.references.$id')->equals($content->getId());

        $query = $qb->getQuery();

        /** @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $request->query->get('limit', 15)
        );

        return $this->render('IntegratedContentBundle:content:used_by.'.$request->getRequestFormat().'.twig', [
            'content' => $content,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param null $filter
     *
     * @return JsonResponse
     */
    public function mediaTypesAction($filter = null)
    {
        $output = [];

        /* @var Image $image */
        foreach ($this->container->get('integrated_content.provider.media')->getContentTypes($filter) as $contentType) {
            $output[] = [
                'id' => $contentType->getId(),
                'name' => $contentType->getName(),
                'path' => $this->generateUrl('integrated_content_content_new', ['type' => $contentType->getId()]),
            ];
        }

        return new JsonResponse($output);
    }

    /**
     * @param ContentTypeInterface $contentType
     * @param ContentInterface     $content
     * @param Request              $request
     *
     * @return FormInterface
     */
    protected function createNewForm(ContentTypeInterface $contentType, ContentInterface $content, Request $request)
    {
        $form = $this->createForm(ContentFormType::class, $content, [
            'action' => $this->generateUrl('integrated_content_content_new', ['type' => $request->get('type'), '_format' => $request->getRequestFormat(), 'relation' => $request->get('relation')]),
            'method' => 'POST',
            'attr' => [
                'class' => 'content-form',
                'data-content-type' => $content->getContentType(),
            ],
            'content_type' => $contentType,
        ]);

        return $form->add('actions', ActionsType::class, ['buttons' => ['create', 'cancel']]);
    }

    /**
     * @param ContentTypeInterface $contentType
     * @param ContentInterface     $content
     * @param array                $locking
     *
     * @return FormInterface
     */
    protected function createEditForm(ContentTypeInterface $contentType, ContentInterface $content, array $locking)
    {
        $form = $this->createForm(ContentFormType::class, $content, [
            'action' => $this->generateUrl(
                'integrated_content_content_edit',
                $locking['lock'] ?
                ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]
                :
                ['id' => $content->getId()]
            ),
            'method' => 'PUT',
            'attr' => [
                'class' => 'content-form',
                'data-content-id' => $content->getId(),
                'data-content-type' => $content->getContentType(),
            ],
            // don't display error's when the content is locked as the user can't save in the first place
            'validation_groups' => $locking['locked'] ? false : null,
            'content_type' => $contentType,
        ]);

        $form->add('returnUrl', HiddenType::class, ['required' => false, 'mapped' => false, 'attr' => ['class' => 'return-url']]);

        // load a different set of buttons based on the permissions and locking state

        if (!$this->get('security.authorization_checker')->isGranted(Permissions::EDIT, $content)) {
            return $form->add('actions', ActionsType::class, ['buttons' => ['back']]);
        }

        if ($locking['locked']) {
            return $form->add('actions', ActionsType::class, ['buttons' => ['reload', 'cancel']]);
        }

        return $form->add('actions', ActionsType::class, ['buttons' => ['save', 'cancel']]);
    }

    /**
     * @param ContentInterface $content
     * @param array            $locking
     * @param bool             $notDelete
     *
     * @return FormInterface
     */
    protected function createDeleteForm(ContentInterface $content, array $locking, $notDelete = false)
    {
        $form = $this->createForm(DeleteFormType::class, null, [
            'action' => $this->generateUrl('integrated_content_content_delete', $locking['locked'] ? ['id' => $content->getId()] : ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]),
            'method' => 'DELETE',
        ]);

        // load a different set of buttons based on the locking state
        if ($locking['locked'] || $notDelete) {
            return $form->add('actions', ActionsType::class, ['buttons' => ['reload', 'cancel']]);
        }

        return $form->add('actions', ActionsType::class, ['buttons' => ['delete', 'cancel']]);
    }

    /**
     * @param ContentInterface $content
     *
     * @return array
     */
    protected function getReferences(ContentInterface $content)
    {
        $references = [];
        /** @var \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation $relation */
        foreach ($content->getRelations() as $relation) {
            foreach ($relation->getReferences() as $reference) {
                $properties = [
                    'id' => $reference->getId(),
                    'title' => method_exists($reference, 'getTitle') ? $reference->getTitle() : $reference->getId(),
                ];

                if ($reference instanceof Image) {
                    $properties['image'] = $this->get('integrated_image.twig_extension')->image($reference->getFile())->cropResize(250, 250)->jpeg();
                }

                $references[$relation->getRelationId()][] = $properties;
            }
        }

        return $references;
    }
}
