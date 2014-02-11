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
use Integrated\Bundle\ContentBundle\Form\Type\DeleteType;
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

		foreach ($this->get('integrated.form.resolver')->getTypes() as $type) {
			$types[$type->getClass()][$type->getType()] = $type;
		}

		foreach (array_keys($types) as $key) {
			ksort($types[$key]);
		}

        /** @var $client \Solarium\Client */
        $client = $this->get('solarium.client');
        $query = $client->createSelect();

		/** @var $paginator \Knp\Component\Pager\Paginator */
		$paginator = $this->get('knp_paginator');
		$paginator = $paginator->paginate(
            array($client, $query),
			$request->query->get('page', 1),
			15
		);

		return array(
			'types' => $types,
			'pager' => $paginator
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
			null,
			array(
				'action' => $this->generateUrl('integrated_content_content_new', ['class' => $request->get('class'), 'type' => $request->get('type')]),
				'method' => 'POST',
			)
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('back')->isClicked()) {
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

				return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]));
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
			array(
				'action' => $this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]),
				'method' => 'PUT',
			)
		);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('back')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}

			if ($form->isValid()) {
				/* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
				$dm = $this->get('doctrine_mongodb')->getManager();
				$dm->flush();

                // Set flash message
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to %s are saved', $type->getType()->getType()));

				return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]));
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
			new DeleteType(),
			null,
			array(
				'action' => $this->generateUrl('integrated_content_content_delete', ['id' => $content->getId()]),
				'method' => 'DELETE',
			)
		);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('back')->isClicked()) {
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

				return $this->redirect($this->generateUrl('integrated_content_content_index'));
			}
		}

		return array(
			'type'    => $type,
			'form'    => $form->createView(),
			'content' => $content
		);
	}
}