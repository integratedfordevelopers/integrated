<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Form\Type\ContentTypePageType;
use Integrated\Bundle\PageBundle\Services\RouteCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var RouteCache
     */
    private $routeCache;

    /**
     * PageController constructor.
     *
     * @param DocumentManager $documentManager
     * @param RouteCache      $routeCache
     */
    public function __construct(DocumentManager $documentManager, RouteCache $routeCache)
    {
        $this->documentManager = $documentManager;
        $this->routeCache = $routeCache;
    }

    /**
     * @param Request         $request
     * @param ContentTypePage $page
     *
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, ContentTypePage $page)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            $this->routeCache->clear();

            $this->addFlash('success', 'Page updated');

            return $this->redirectToRoute('integrated_page_page_index');
        }

        return $this->render('@IntegratedPage/content_type_page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param ContentTypePage $page
     *
     * @return FormInterface
     */
    protected function createEditForm(ContentTypePage $page)
    {
        $form = $this->createForm(
            ContentTypePageType::class,
            $page,
            [
                'method' => 'PUT',
                'controller' => $this->container->get($page->getControllerService()),
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
    }
}
