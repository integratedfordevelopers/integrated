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
	public function indexAction()
	{
		// group the types based on there class
		$types = array();

		foreach ($this->get('integrated.form.resolver')->getTypes() as $type) {
			$types[$type->getClass()][$type->getType()] = $type;
		}

		foreach (array_keys($types) as $key) {
			ksort($types[$key]);
		}

		return array(
			'types' => $types
		);
	}

//	/**
//	 * Create a new document
//	 *
//	 * @Template()
//	 * @param Request $request
//	 * @return array
//	 */
//	public function newAction(Request $request)
//	{
//		$type = $this->get('integrated.form.resolver')->getType($request->get('class'), $request->get('type'));
//
//		$form = $this->createForm(
//			$this->get('integrated.form.factory')->getType($request->get('class'), $request->get('type')),
//			null,
//			array(
//				'action' => $this->generateUrl('integrated_content_content_create'),
//				'method' => 'POST',
//			)
//		);
//
//		return array(
//			'type' => $type,
//			'form' => $form->createView()
//		);
//	}

	/**
	 * Create a new document
	 *
	 * @Template()
	 * @param Request $request
	 * @return array | Response
	 */
	public function newAction(Request $request)
	{
		$type = $this->get('integrated.form.resolver')->getType($request->get('class'), $request->get('type'));

		$form = $this->createForm(
			$this->get('integrated.form.factory')->getType($request->get('class'), $request->get('type')),
			null,
			array(
				'action' => $this->generateUrl('integrated_content_content_create', ['class' => $type->getClass(), 'type' => $type->getType()]),
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

				return $this->redirect($this->generateUrl('integrated_content_content_edit', ['id' => $content->getId()]));
			}
		}

		return array(
			'type' => $type,
			'form' => $form->createView()
		);
	}

	/**
	 * Create a new document
	 *
	 * @Template()
	 * @param Request $request
	 * @return array | Response
	 */
	public function editAction(Request $request)
	{
		return array();
	}
}