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

//use Integrated\Common\Content\ContentInterface;
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentController extends Controller
{
	/**
	 *
	 *
	 * @Template()
	 * @return array
	 */
	public function indexAction(Request $request)
	{
		// group the types based on there class
		$types = array();

        // Store contentTypes in array
        $displayTypes = array();

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

        if ($q = $request->get('q')) {
            $dismax = $query->getDisMax();
            $dismax->setQueryFields('title content');

            $query->setQuery($q);
        }

        // Execute the query
        $result = $client->select($query);

		/** @var $paginator \Knp\Component\Pager\Paginator */
		$paginator = $this->get('knp_paginator');
		$paginator = $paginator->paginate(
            array($client, $query),
			$request->query->get('page', 1),
			$request->query->get('limit', 15)
		);

		return array(
			'types' => $types,
			'pager' => $paginator,
            'contentTypes' => $displayTypes,
            'active' => $contentType,
            'facets' => $result->getFacetSet()->getFacets()
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
		$type = $this->get('integrated.form.factory')->getType($request->get('class'), $request->get('type'));

		$form = $this->createForm(
			$type,
			$type->getType()->create(),
			[
				'action' => $this->generateUrl('integrated_content_content_new', ['class' => $request->get('class'), 'type' => $request->get('type')]),
				'method' => 'POST',
			],
			[
				'create' => ['type' => 'submit', 'options' => ['label' => 'Create']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}

			if ($form->isValid()) {
				/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
				$dm = $this->get('doctrine_mongodb')->getManager();

				$content = $form->getData();

				$dm->persist($content);
				$dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('A new %s is created', $type->getType()->getType()));

                //TODO: improve this. JSM and JvL are gonna kick me if they see this
                $indexer = $this->get('integrated_solr.indexer');
                $indexer->execute();
                file_get_contents('http://' . $this->container->getParameter('solr_host') . ':' . $this->container->getParameter('solr_port') . '/solr/' . $this->container->getParameter('solr_core') . '/update?commit=true');

                return $this->redirect($this->generateUrl('integrated_content_content_index'));
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

		$form = $this->createForm(
			$type,
			$content,
			[
				'action' => $this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]),
				'method' => 'PUT',
			],
			[
				'save' => ['type' => 'submit', 'options' => ['label' => 'Save']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}

			if ($form->isValid()) {
				/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
				$dm = $this->get('doctrine_mongodb')->getManager();
				$dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to %s are saved', $type->getType()->getType()));

                //TODO: improve this. JSM and JvL are gonna kick me if they see this
                $indexer = $this->get('integrated_solr.indexer');
                $indexer->execute();

                return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}
		}

		return array(
			'type'    => $type->getType(),
			'form'    => $form->createView(),
			'content' => $content
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
		$type = $this->get('integrated.form.resolver')->getType(get_class($content), $content->getContentType());

		$form = $this->createForm(
			new DeleteFormType(),
			$content,
			[
				'action' => $this->generateUrl('integrated_content_content_delete', ['id' => $content->getId()]),
				'method' => 'DELETE',
			],
			[
				'delete' => ['type' => 'submit', 'options' => ['label' => 'Delete']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}

			if ($form->isValid()) {
				/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
				$dm = $this->get('doctrine_mongodb')->getManager();

				$dm->remove($content);
				$dm->flush();

				$this->get('session')->getFlashBag()->add('notice', array(
					'head' => 'Removed!',
					'body' => sprintf('The %s is removed', $type->getType())
				));

                //TODO: improve this. JSM and JvL are gonna kick me if they see this
                $indexer = $this->get('integrated_solr.indexer');
                $indexer->execute();
                file_get_contents('http://' . $this->container->getParameter('solr_host') . ':' . $this->container->getParameter('solr_port') . '/solr/' . $this->container->getParameter('solr_core') . '/update?commit=true');

                return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}
		}

		return array(
			'type'    => $type,
			'form'    => $form->createView(),
			'content' => $content
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