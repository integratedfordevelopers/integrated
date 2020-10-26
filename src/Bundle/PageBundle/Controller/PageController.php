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
use Doctrine\ODM\MongoDB\Query\Builder;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Form\Type\PageCopyType;
use Integrated\Bundle\PageBundle\Form\Type\PageFilterType;
use Integrated\Bundle\PageBundle\Form\Type\PageType;
use Integrated\Bundle\PageBundle\Services\PageCopyService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var PageCopyService
     */
    private $pageCopyService;

    /**
     * PageController constructor.
     *
     * @param DocumentManager $documentManager
     * @param PageCopyService $pageCopyService
     */
    public function __construct(DocumentManager $documentManager, PageCopyService $pageCopyService)
    {
        $this->documentManager = $documentManager;
        $this->pageCopyService = $pageCopyService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $filterForm = $this->createForm(PageFilterType::class, null, ['method' => 'GET']);
        $filterForm->handleRequest($request);

        switch ($filterForm->get('pagetype')->getData()) {
            case 'page':
                $class = Page::class;
                break;
            case 'contenttype':
                $class = ContentTypePage::class;
                break;
            default:
                $class = AbstractPage::class;
        }

        $builder = $this->documentManager->createQueryBuilder($class);

        $this->displayPathErrors($builder);

        if ($query = $filterForm->get('q')->getData()) {
            $builder->addOr($builder->expr()->field('title')->equals(new \MongoRegex('/'.$query.'/i')));
            $builder->addOr($builder->expr()->field('path')->equals(new \MongoRegex('/'.$query.'/i')));
        }

        if ($channel = $filterForm->get('channel')->getData()) {
            $builder->field('channel.$id')->equals($channel);
        }

        $builder->sort('path', 1);
        $builder->sort('channel.$id', 1);

        $pagination = $this->get('knp_paginator')->paginate(
            $builder,
            $request->query->get('page', 1),
            25
        );

        $response = $this->render('IntegratedPageBundle:page:index.html.twig', [
            'pages' => $pagination,
            'filterForm' => $filterForm->createView(),
        ]);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function newAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $page = new Page();

        $form = $this->createCreateForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($page);
            $this->documentManager->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return $this->render('IntegratedPageBundle:page:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Page    $page
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, Page $page)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return $this->render('IntegratedPageBundle:page:edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Page    $page
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction(Request $request, Page $page)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($page->isLocked()) {
            throw $this->createNotFoundException(sprintf('Page "%s" is locked.', $page->getId()));
        }

        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->remove($page);
            $this->documentManager->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_page_page_index'));
        }

        return $this->render('IntegratedPageBundle:page:delete.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function copyAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($formData = $request->request->get('page_copy', null)) {
            $targetChannel = $formData['targetChannel'] ?? null;
            $sourceChannel = $formData['sourceChannel'] ?? null;
        } else {
            $targetChannel = null;
            $sourceChannel = null;
        }

        $form = $this->createForm(
            PageCopyType::class,
            null,
            [
                'sourceChannel' => $sourceChannel,
                'targetChannel' => $targetChannel,
                'action' => $this->generateUrl('integrated_page_page_copy'),
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['action'] != 'refresh') {
                $this->pageCopyService->copyPages($form->getData());

                $this->get('braincrafted_bootstrap.flash')->success('Pages copied');

                return $this->redirect($this->generateUrl('integrated_page_page_index'));
            }
        }

        return $this->render('IntegratedPageBundle:page:copy.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Page $page
     *
     * @return FormInterface
     */
    protected function createCreateForm(Page $page)
    {
        $form = $this->createForm(
            PageType::class,
            $page,
            [
                'action' => $this->generateUrl('integrated_page_page_new'),
                'method' => 'POST',
            ]
        );

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_page_page_index',
            'label' => 'Create',
            'button_class' => '',
        ]);

        return $form;
    }

    /**
     * @param Page $page
     *
     * @return FormInterface
     */
    protected function createEditForm(Page $page)
    {
        $form = $this->createForm(
            PageType::class,
            $page,
            [
                'action' => $this->generateUrl(
                    'integrated_page_page_edit',
                    ['id' => $page->getId()]
                ),
                'method' => 'PUT',
            ]
        );

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_page_page_index',
        ]);

        return $form;
    }

    /**
     * @param string $id
     *
     * @return FormInterface
     */
    protected function createDeleteForm($id)
    {
        $builder = $this->createFormBuilder();

        $builder->setAction($this->generateUrl('integrated_page_page_delete', ['id' => $id]));
        $builder->setMethod('DELETE');
        $builder->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]);

        return $builder->getForm();
    }

    /**
     * @param Builder $builder
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    protected function displayPathErrors(Builder $builder)
    {
        $paths = [];
        foreach ($builder->getQuery()->execute() as $item) {
            if (!$item instanceof ContentTypePage) {
                continue;
            }

            $settings = $item->getControllerService().$item->getLayout();
            $key = $item->getChannel()->getId().'-'.$item->getPath();
            if (isset($paths[$key]) && $paths[$key] != $settings) {
                $this->get('braincrafted_bootstrap.flash')->error('Path '.$item->getPath().' is used multiple times with diffent settings. Only one will be used');
                continue;
            }

            $paths[$key] = $settings;
        }
    }
}
