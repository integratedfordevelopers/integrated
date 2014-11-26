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

use Traversable;

use Integrated\Bundle\ContentBundle\Document\Content\Content;

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

use Integrated\Common\Locks;
use Integrated\Common\Security\Permissions;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentController extends Controller
{
	/**
	 * @Template()
	 * @return array
	 */
	public function indexAction(Request $request)
	{
		// group the types based on there class
		$types = array();

        // Store contentTypes in array
        $displayTypes = array();

        //remember search state
        $session = $request->getSession();
        if ($request->query->get('remember') && $session->has('content_index_view')) {
            $request->query->add(unserialize($session->get('content_index_view')));
            $request->query->remove('remember');
        }
        elseif (!$request->query->has('_format')) {
            $session->set('content_index_view',serialize($request->query->all()));
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


        if ($request->isMethod('post')) {
            $id = (array) $request->get('id');
            if (is_array($id)) {

                if (count($id) == 0) {
                    $id[] = '';
                }

                if (count($id)) {
                    $helper = $query->getHelper();
                    $filter = function($param) use($helper) {
                        return $helper->escapePhrase($param);
                    };

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

        $query->addSort($sort_options[$sort]['field'], in_array($request->query->get('order'),$order_options) ? $request->query->get('order') : $sort_options[$sort]['order'] );

		// Execute the query
		$result = $client->select($query);

		/** @var $paginator \Knp\Component\Pager\Paginator */
		$paginator = $this->get('knp_paginator');
		$paginator = $paginator->paginate(
            array($client, $query),
			$request->query->get('page', 1),
			$request->query->get('limit', 15),
			['sortFieldParameterName' => null]
		);

        /** @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();
        $channels = array();
        if ($channelResult = $dm->getRepository('Integrated\\Bundle\\ContentBundle\\Document\\Channel\\Channel')->findAll()) {
            /** @var $channel \Integrated\Bundle\ContentBundle\Document\Channel\Channel */
            foreach ($channelResult as $channel) {
                $channels[$channel->getId()] = $channel->getName();
            }
        }

		return array(
			'types'        => $types,
			'params'       => ['sort' => ['current' => $sort, 'default' => $sort_default, 'options' => $sort_options]],
			'pager'        => $paginator,
            'contentTypes' => $displayTypes,
            'active'       => array('contenttypes' => $contentType, 'channels' => $activeChannels),
            'channels'     => $channels,
            'facets'       => $result->getFacetSet()->getFacets(),
			'locks'        => $this->getLocks($paginator)
		);
	}

	/**
	 * Create a new document
	 *
	 * @Template()
	 * @param Request $request
	 * @return array | Response
	 */
	public function newAction(Request $request)
	{
		/** @var $type \Integrated\Common\Content\Form\FormTypeInterface */
		$type = $this->get('integrated.form.factory')->getType($request->get('type'));

		$content = $type->getType()->create();

		if (!$this->get('security.context')->isGranted(Permissions::CREATE, $content)) {
			throw new AccessDeniedException();
		}

		$form = $this->createForm(
			$type,
			$content,
			[
				'action' => $this->generateUrl('integrated_content_content_new', ['class' => $request->get('class'), 'type' => $request->get('type')]),
				'method' => 'POST',
			],
			[
				'create' => ['type' => 'submit', 'options' => ['label' => 'Create']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate']]],
			]
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
			}

			if ($form->isValid()) {
				/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
				$dm = $this->get('doctrine_mongodb')->getManager();

				$dm->persist($content);
				$dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success(
                    $this->get('translator')->trans('The document %name% has been created', array('%name%' => $type->getType()->getName()))
                );

                if ($this->has('integrated_solr.indexer')) {
					$indexer = $this->get('integrated_solr.indexer');
					$indexer->setOption('queue.size', 2);
					$indexer->execute(); // lets hope that the gods of random is in our favor as there is no way to guarantee that this will do what we want
				}

                return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
			}
		}

		return array(
			'type' => $type->getType(),
			'form' => $form->createView()
		);
	}

	/**
	 * Update a existing document
	 *
	 * @Template()
	 * @param Request $request
	 * @param Content $content
	 * @return array | Response
	 */
	public function editAction(Request $request, Content $content)
	{
		/** @var $type \Integrated\Common\Content\Form\FormTypeInterface */
		$type = $this->get('integrated.form.factory')->getType($content);

		if (!$this->get('security.context')->isGranted(Permissions::EDIT, $content)) {
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

		// load a different set of buttons based bases on the locking stat for this
		// content object

		if ($locking['locked']) {
			$buttons = [
				'reload' => ['type' => 'submit', 'options' => ['label' => 'Reload']],
				'reload_changed' => ['type' => 'submit', 'options' => ['label' => 'Reload (keep changes)', 'attr' => ['type' => 'default']]],
                'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate']]],
			];
		} else {
			$buttons = [
				'save' => ['type' => 'submit', 'options' => ['label' => 'Save']],
                'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate']]],
			];
		}

		$form = $this->createForm($type, $content, [
            'action' => $this->generateUrl('integrated_content_content_edit', $locking['locked'] ? ['id' => $content->getId()] : ['id' => $content->getId(), 'lock' => $locking['lock']->getId()]),
			'method' => 'PUT',

			// don't display error's when the content is locked as the user can't save in the first place
			'validation_groups' => $locking['locked'] ? false : null
		], $buttons);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// possible actions are cancel, reload, reload_changed and save

			$actions = $form->get('actions');

			if ($actions->get('cancel')->isClicked()) {
				if (!$locking['locked']) {
					$locking['release']();
				}

				return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
			}

			if ($actions->has('reload') && $actions->get('reload')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]));
			}

			if ($actions->has('save') && $actions->get('save')->isClicked()) {
				if (!$locking['locked'] && $form->isValid()) {
					/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
					$dm = $this->get('doctrine_mongodb')->getManager();
					$dm->flush();

	                // Set flash message
	                $this->get('braincrafted_bootstrap.flash')->success(
                        $this->get('translator')->trans('The changes to %name% are saved', array('%name%' => $type->getType()->getName()))
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

			// reload_changed is just submitting without saving so the changes made are
			// not lost and there is a new change to get a lock on the content.
		}

		if ($locking['locked']) {
			// the document is locked so display display a error message explaining that
			// the user can not edit this page will the lock is there.

			if ($locking['owner']) {
				$text = 'The document is currently locked by your self in a different browser or tab and can not be edited until this lock is released.';
			} else if ($locking['user']) {
				$user = $locking['user']->getUsername();

				// we got a basic user name now try to get a better one

				if (method_exists($locking['user'], 'getRelation')) {
					if ($relation = $locking['user']->getRelation()) {
						if (method_exists($relation,'__toString')) {
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

		return array(
			'type'    => $type->getType(),
			'form'    => $form->createView(),
			'content' => $content,
			'locking' => $locking
		);
	}

	/**
	 * Delete a document
	 *
	 * @Template()
	 * @param Request $request
	 * @param Content $content
	 * @return array | Response
	 */
	public function deleteAction(Request $request, Content $content)
	{
		/** @var $type \Integrated\Common\ContentType\ContentTypeInterface */
		$type = $this->get('integrated.form.resolver')->getType($content->getContentType());

		if (!$this->get('security.context')->isGranted(Permissions::DELETE, $content)) {
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

		// load a different set of buttons based bases on the locking stat for this
		// content object

		if ($locking['locked'] && (!$request->isMethod('delete'))) {
			$buttons = [
				'reload' => ['type' => 'submit', 'options' => ['label' => 'Retry']],
                'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate']]],
			];
		} else {
			$buttons = [
				'delete' => ['type' => 'submit', 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'button_class' => 'default', 'attr' => ['formnovalidate' => 'formnovalidate']]],
			];
		}

		$form = $this->createForm('content_delete', $content, [
			'action' => $this->generateUrl('integrated_content_content_delete', ['id' => $content->getId()]),
			'method' => 'DELETE',
		], $buttons);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// possible actions are cancel, reload and delete

			$actions = $form->get('actions');

			// check for back click else its a submit
			if ($actions->get('cancel')->isClicked()) {
				if (!$locking['locked']) {
					$locking['release']();
				}

				return $this->redirect($this->generateUrl('integrated_content_content_index', ['remember' => 1]));
			}

			if ($actions->has('reload') && $actions->get('reload')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_delete', ['id' => $content->getId()]));
			}

			if ($actions->has('delete') && $actions->get('delete')->isClicked()) {
				if ($form->isValid()) {
					/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
					$dm = $this->get('doctrine_mongodb')->getManager();

					$dm->remove($content);
					$dm->flush();

                    // Set flash message
                    $this->get('braincrafted_bootstrap.flash')->success(
                        $this->get('translator')->trans('The document %name% has been deleted', array('%name%' => $type->getName()))
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
			} else if ($locking['user']) {
				$user = $locking['user']->getUsername();

				// we got a basic user name now try to get a better one

				if (method_exists($locking['user'], 'getRelation')) {
					if ($relation = $locking['user']->getRelation()) {
						if (method_exists($relation,'__toString')) {
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

		return array(
			'type'    => $type,
			'form'    => $form->createView(),
			'content' => $content,
			'locking' => $locking
		);
	}

	/**
	 * Get a lock or find out who does have the lock.
	 *
	 * The result is a array with the following keys:
	 * - lock: this will contain the instance of the lock object or null.
	 * - user: this is the user the lock belongs to or null if the lock does
	 *         not have a owner.
	 *
	 * @param object $object
	 * @param int | null $timeout
	 *
	 * @return array
	 */
	protected function getLock($object, $timeout = null)
	{
		if (!$this->has('integrated_locking.dbal.manager')) {
			return [
				'lock'    => null,
				'user'    => null,
				'owner'   => false,
				'new'     => false,
				'release' => function() {}
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
					'lock'    => $lock,
					'user'    => $this->getUser(),
					'owner'   => true,
					'new'     => true,
					'release' => function() use ($service, $lock) {
						$service->release($lock);
					}
				];
			}
		} // can not acquire a lock if not logged in.

		if ($lock = $service->findByResource($object)) {
			$lock = $lock[0];

			if ($owner && $owner->equals($lock->getRequest()->getOwner())) {
				return [
					'lock'    => $lock,
					'user'    => $this->getUser(),
					'owner'   => true,
					'new'     => false,
					'release' => function() use ($service, $lock) {
						$service->release($lock);
					}
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
				'lock'    => $lock,
				'user'    => $user,
				'owner'   => false,
				'new'     => false,
				'release' => function() use ($service, $lock) {
					$service->release($lock);
				}
			];
		}

		return [
			'lock'  => null,
			'user'  => null,
			'owner' => false,
			'new'   => false,
			'release' => function() {}
		];
	}

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
						if (method_exists($relation,'__toString')) {
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
     * @Template()
     * @param Request $request
     * @return array
     */
    public function navdropdownsAction(Request $request)
    {

        $session = $request->getSession();

        $queuecount = (int) $this->container->get('integrated_queue.dbal.provider')->count();
        $queuepercentage = 100;
        if ($queuecount > 0) {
            $queuemaxcount = max($queuecount,$session->get('queuemaxcount'));
            $session->set('queuemaxcount',$queuemaxcount);
            $queuepercentage = round(($queuemaxcount-$queuecount) / $queuemaxcount * 100);
        }
        else {
            $session->remove('queuemaxcount');
        }

        $email = '';
//        if ($this->getUser()->getRelation() && $email = $this->getUser()->getRelation()->getEmail()) {
//
//        }

        $avatarurl = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $email ) ) ) . "?s=45";

        return array(
            'avatarurl' => $avatarurl,
            'queuecount' => $queuecount,
            'queuepercentage' => $queuepercentage,
        );
    }

    /**
     * @param Content $content
     * @param Request $request
     * @author Jeroen van Leeuwen <jeroen@e-active.nl>
     * @return array
     * @Template()
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

        return array(
            'content' => $content,
            'pagination' => $pagination
        );
    }

	/**
	 * @inheritdoc
	 */
	public function createForm($type, $data = null, array $options = [], array $buttons = [])
	{
		/** @var FormBuilder $form */
		$form = $this->container->get('form.factory')->createBuilder($type, $data, $options);

		if ($buttons) {
			$form->add('actions', 'form_actions', [
				'buttons' => $buttons
			]);
		}

		return $form->getForm();
	}
}
