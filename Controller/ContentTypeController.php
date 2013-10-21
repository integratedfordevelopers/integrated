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

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

class ContentTypeController extends Controller
{
    /**
     * @var string
     */
    protected $contentTypeClass = 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\ContentType';

    /**
     * Lists all the ContentType documents
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->get('doctrine_mongodb')->getManager();

        $documents = $dm->getRepository($this->contentTypeClass)->findAll();

        return $this->render('IntegratedContentBundle:ContentType:index.html.twig', array('documents' => $documents));
    }

    /**
     * Finds and displays a ContentType document
     *
     * @param ContentType $contentType
     * @return array
     */
    public function showAction(ContentType $contentType)
    {
        return array(
            'contentType' => $contentType
        );
    }

    /**
     * Display a list of Content documents
     */
    public function selectAction()
    {

    }

    /**
     * Displays a form to create a new ContentType document
     */
    public function newAction()
    {

    }

    /**
     * Creates a new ContentType document
     */
    public function createAction(Request $request)
    {

    }

    /**
     * Display a form to edit an existing ContentType document
     */
    public function editAction(ContentType $contentType)
    {

    }

    /**
     * Edits an existing ContentType document
     *
     * @param Request $request
     * @param ContentType $contentType
     */
    public function updateAction(Request $request, ContentType $contentType)
    {

    }

    public function deleteAction(Request $request, ContentType $contentType)
    {

    }
}