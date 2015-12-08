<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Page $page)
    {
        return $this->render($page->getLayout(), [
            'page' => $page,
            'edit' => false,
        ]);
    }

    /**
     * @param Request $request
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Page $page)
    {
        // @todo security check (INTEGRATED-383)
        // @todo use json (INTEGRATED-515)

        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDocumentManager()->flush();

            return $this->redirect($this->generateUrl('integrated_website_page_' . $page->getId()));
        }

        return $this->render($page->getLayout(), [
            'page' => $page,
            'form' => $form->createView(),
            'edit' => true,
        ]);
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(Page $page)
    {
        return $this->createForm(
            'integrated_website_page',
            $page,
            [
                'action' => $this->generateUrl('integrated_website_page_edit', ['id' => $page->getId()]),
                'method' => 'POST',
            ]
        );
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->get('doctrine_mongodb')->getManager();
    }
}
