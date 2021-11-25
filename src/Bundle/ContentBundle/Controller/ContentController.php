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

use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Integrated\Bundle\ImageBundle\Twig\Extension\ImageExtension;
use Integrated\Bundle\IntegratedBundle\Controller\AbstractController;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Locks\Provider\DBAL\Manager;
use Integrated\Common\Locks\Resource;
use Integrated\Common\Locks\Filter;
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
use Integrated\Common\Solr\Indexer\IndexerInterface;
use Integrated\MongoDB\Solr\Indexer\QueueSubscriber;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Traversable;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentController extends AbstractController
{
    /**
     * @var string
     */
    protected $relationClass = 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation';

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var QueueSubscriber
     */
    private $queueSubscriber;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var IndexerInterface
     */
    private $indexer;
    /**
     * @var SearchContentReferenced
     */
    private $contentReferenced;

    /**
     * @var Manager
     */
    private $lockManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var ImageExtension
     */
    private $imageExtension;

    public function __construct(
        ResolverInterface $resolver,
        ContentTypeManager $contentTypeManager,
        QueueSubscriber $queueSubscriber,
        LockFactory $lockFactory,
        IndexerInterface $indexer,
        SearchContentReferenced $contentReferenced,
        Manager $lockManager,
        UserManagerInterface $userManager,
        ImageExtension $imageExtension
    ) {
        $this->resolver = $resolver;
        $this->contentTypeManager = $contentTypeManager;
        $this->queueSubscriber = $queueSubscriber;
        $this->lockFactory = $lockFactory;
        $this->indexer = $indexer;
        $this->contentReferenced = $contentReferenced;
        $this->lockManager = $lockManager;
        $this->userManager = $userManager;
        $this->imageExtension = $imageExtension;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
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
        foreach ($this->resolver->getTypes() as $type) {
            $types[$type->getClass()][$type->getId()] = $type;
            $displayTypes[$type->getId()] = $type->getName();
        }

        foreach (array_keys($types) as $key) {
            ksort($types[$key]);
        }

        /** @var $client \Solarium\Client */
        $client = $this->getSolarium();
        $client->getPlugin('postbigrequest');

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
            $dm = $this->getDoctrineODM()->getManager();

            /** @var Relation $relation */
            if ($relation = $dm->getRepository($this->relationClass)->find($relation)) {
                foreach ($relation->getTargets() as $target) {
                    $contentType[] = $target->getId();
                    $relations[] = [
                        'href' => $this->generateUrl('integrated_content_content_new', ['class' => $target->getClass(), 'type' => $target->getId(), 'relation' => $relation->getId()]),
                        'name' => $target->getName(),
                    ];
                }
            }
        } else {
            $contentType = $request->query->get('contenttypes');
        }

        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDoctrineODM()->getManager();

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

        $filterWorkflow = [];

        $user = $this->getUser();

        if ($user instanceof GroupableInterface) {
            foreach ($user->getGroups() as $group) {
                $filterWorkflow[] = $group->getId();
            }
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
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
            'rank' => ['name' => 'rank', 'field' => 'rank', 'label' => 'rank', 'order' => 'asc'],
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
        $sort = \array_key_exists($sort, $sort_options) ? $sort : $sort_default;

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
        $paginator = $this->getPaginator();
        $paginator = $paginator->paginate(
            [$client, $query],
            $request->query->get('page', 1),
            $request->query->get('limit', 15),
            ['sortFieldParameterName' => null]
        );

        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDoctrineODM()->getManager();
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
    public function show(Request $request, Content $content)
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
    public function new(Request $request)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->contentTypeManager->getType($request->get('type'));

        $content = $contentType->create();

        if (!$this->isGranted(Permissions::CREATE, $content)) {
            throw new AccessDeniedException();
        }

        $form = $this->createNewForm($contentType, $content, $request);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->getData() == 'cancel') {
                return $this->redirectToRoute('integrated_content_content_index', ['remember' => 1]);
            }

            if ($form->isValid()) {
                //higher priority for content edited in Integrated
                $queue = $this->queueSubscriber->getQueue();
                $this->queueSubscriber->setPriority($queue::PRIORITY_HIGH);

                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->getDoctrineODM()->getManager();

                $dm->persist($content);
                $dm->flush();

                $lock = $this->lockFactory->createLock(self::class);
                $lock->acquire(true);

                try {
                    $this->indexer->setOption('queue.size', 2);
                    $this->indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
                } finally {
                    $lock->release();
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
                    $this->getTranslator()->trans('The document %name% has been created', ['%name%' => $contentType->getName()])
                );

                return $this->redirectToRoute('integrated_content_content_index', ['remember' => 1]);
            }
        }

        return $this->render(sprintf('IntegratedContentBundle:content:new.%s.twig', $request->getRequestFormat()), [
            'editable' => true,
            'type' => $contentType,
            'form' => $form->createView(),
            'showContentHistory' => false,
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
    public function edit(Request $request, Content $content)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->contentTypeManager->getType($content->getContentType());

        if (!$this->isGranted(Permissions::VIEW, $content)) {
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
                    $parameters = array_merge($request->query->all(), [
                        'id' => $content->getId(),
                        'lock' => $locking['lock']->getId(),
                    ]);

                    return $this->redirectToRoute('integrated_content_content_edit', $parameters);
                }

                $locking['locked'] = false;
            }
        }

        $form = $this->createEditForm($contentType, $content, $locking, $request);

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

            if (!$this->isGranted(Permissions::EDIT, $content)) {
                throw new AccessDeniedException();
            }

            if ($form->get('actions')->getData() == 'reload') {
                return $this->redirectToRoute('integrated_content_content_edit', ['id' => $content->getId()]);
            }

            // this is not rest compatible since a button click is required to save
            if ($form->get('actions')->getData() == 'save') {
                if (!$locking['locked'] && $form->isValid()) {
                    //higher priority for content edited in Integrated
                    $queue = $this->queueSubscriber->getQueue();
                    $this->queueSubscriber->setPriority($queue::PRIORITY_HIGH);

                    /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                    $dm = $this->getDoctrineODM()->getManager();
                    $dm->flush();

                    // Set flash message
                    $this->get('braincrafted_bootstrap.flash')->success(
                        $this->getTranslator()->trans('The changes to %name% are saved', ['%name%' => $contentType->getName()])
                    );

                    $lock = $this->lockFactory->createLock(self::class);
                    $lock->acquire(true);

                    try {
                        $this->indexer->setOption('queue.size', 2);
                        $this->indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
                    } finally {
                        $lock->release();
                    }

                    if (!$locking['locked']) {
                        $locking['release']();
                    }

                    return $this->redirectToRoute('integrated_content_content_index', ['remember' => 1]);
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
            'editable' => $this->isGranted(Permissions::EDIT, $content),
            'type' => $contentType,
            'form' => $form->createView(),
            'content' => $content,
            'locking' => $locking,
            'showContentHistory' => true,
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
    public function delete(Request $request, Content $content)
    {
        /** @var $type \Integrated\Common\ContentType\ContentTypeInterface */
        $type = $this->resolver->getType($content->getContentType());

        if (!$this->isGranted(Permissions::DELETE, $content)) {
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
                    return $this->redirectToRoute('integrated_content_content_delete', ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]);
                }

                $locking['locked'] = false;
            }
        }

        $referenced = $this->contentReferenced->getReferenced($content);

        $form = $this->createDeleteForm($content, $locking, \count($referenced) > 0);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // possible actions are cancel, reload and delete

            if ($form->get('actions')->getData() == 'cancel') {
                if (!$locking['locked']) {
                    $locking['release']();
                }

                return $this->redirectToRoute('integrated_content_content_index', ['remember' => 1]);
            }

            if ($form->get('actions')->getData() == 'reload') {
                return $this->redirectToRoute('integrated_content_content_delete', ['id' => $content->getId()]);
            }

            // this is not rest compatible since a button click is required to save
            if ($form->get('actions')->getData() == 'delete') {
                if ($form->isValid()) {
                    //higher priority for content edited in Integrated
                    $queue = $this->queueSubscriber->getQueue();
                    $this->queueSubscriber->setPriority($queue::PRIORITY_HIGH);

                    /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                    $dm = $this->getDoctrineODM()->getManager();

                    $dm->remove($content);
                    $dm->flush();

                    // Set flash message
                    $this->get('braincrafted_bootstrap.flash')->success(
                        $this->getTranslator()->trans('The document %name% has been deleted', ['%name%' => $type->getName()])
                    );

                    $this->indexer->setOption('queue.size', 2);
                    $this->indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want

                    if (!$locking['locked']) {
                        $locking['release']();
                    }

                    return $this->redirectToRoute('integrated_content_content_index', ['remember' => 1]);
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
     * @param object   $object
     * @param int|null $timeout
     *
     * @return array
     */
    protected function getLock($object, $timeout = null)
    {
        if (!$this->isGranted(Permissions::EDIT, $object)) {
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
        $service = $this->lockManager;

        // Remove expired locks
        $service->clean();

        $object = Resource::fromObject($object);
        $owner = null;

        if ($user = $this->getUser()) {
            $owner = Resource::fromAccount($user);
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
                if ($this->userManager->getClassName() === $owner->getType()) {
                    $user = $this->userManager->findByUsername($owner->getIdentifier());
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

        $filter = new Filter();

        foreach ($iterator as $data) {
            $filter->resources[] = new Resource($data['type_class'], $data['type_id']);
        }

        if (!$filter->resources) {
            return $results;
        }

        foreach ($this->lockManager->findBy($filter) as $lock) {
            // get the user the locks belongs to.
            $user = null;

            if ($owner = $lock->getRequest()->getOwner()) {
                if ($this->userManager->getClassName() === $owner->getType()) {
                    $user = $this->userManager->findByUsername($owner->getIdentifier());
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
    public function navdropdowns(Request $request)
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
        $query = $this->getSolarium()->createSelect();

        $assignedContent = [];

        if ($user = $this->getUser()) {
            $userId = $user->getId();

            $query
                ->createFilterQuery('workflow_assigned_id')
                ->setQuery('facet_workflow_assigned_id:'.$userId.'');

            $result = $this->getSolarium()->select($query);

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
    public function usedBy(Content $content, Request $request)
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDoctrineODM()->getManager();

        $qb = $dm->createQueryBuilder('IntegratedContentBundle:Content\Content');
        $qb->field('relations.references.$id')->equals($content->getId());

        $query = $qb->getQuery();

        /** @var $paginator \Knp\Component\Pager\Paginator */
        $pagination = $this->getPaginator()->paginate(
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
                'path' => $this->generateUrl('integrated_content_content_new', ['type' => $contentType->getId(), '_format' => 'iframe.html']),
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
        $parameters = array_merge($request->query->all(), [
            'type' => $request->get('type'),
            '_format' => $request->getRequestFormat(),
            'relation' => $request->get('relation'),
        ]);

        $form = $this->createForm(ContentFormType::class, $content, [
            'action' => $this->generateUrl('integrated_content_content_new', $parameters),
            'method' => 'POST',
            'attr' => [
                'class' => 'content-form',
                'data-content-type' => $contentType->getId(),
            ],
            'content_type' => $contentType,
        ]);

        return $form->add('actions', ActionsType::class, ['buttons' => ['create', 'cancel']]);
    }

    /**
     * @param ContentTypeInterface $contentType
     * @param ContentInterface     $content
     * @param array                $locking
     * @param Request|null         $request
     *
     * @return FormInterface
     */
    protected function createEditForm(ContentTypeInterface $contentType, ContentInterface $content, array $locking, Request $request = null)
    {
        $parameters = ($locking['lock'] ? ['id' => $content->getId(), 'lock' => $locking['lock']->getId()] : ['id' => $content->getId()]);

        if ($request instanceof Request) {
            $parameters = array_merge($request->query->all(), $parameters);
        }

        $options = [
            'action' => $this->generateUrl(
                'integrated_content_content_edit',
                $parameters
            ),
            'method' => 'PUT',
            'attr' => [
                'class' => 'content-form',
                'data-content-id' => $content->getId(),
                'data-content-type' => $contentType->getId(),
            ],
            'content_type' => $contentType,
        ];

        if ($locking['locked']) {
            // don't display error's when the content is locked as the user can't save in the first place
            $options['validation_groups'] = false;
        }

        $form = $this->createForm(ContentFormType::class, $content, $options);
        $form->add('returnUrl', HiddenType::class, ['required' => false, 'mapped' => false, 'attr' => ['class' => 'return-url']]);

        // load a different set of buttons based on the permissions and locking state

        if (!$this->isGranted(Permissions::EDIT, $content)) {
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
                    'title' => (string) $reference,
                ];

                if ($reference instanceof Image) {
                    $properties['image'] = $this->imageExtension->image($reference->getFile())->cropResize(250, 250)->jpeg();
                }

                $references[$relation->getRelationId()][] = $properties;
            }
        }

        return $references;
    }
}
