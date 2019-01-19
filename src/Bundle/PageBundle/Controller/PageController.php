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

use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Form\Type\PageType;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $channel = $this->getSelectedChannel();

        $builder = $this->getDocumentManager()->createQueryBuilder(Page::class)
            ->field('channel.$id')->equals($channel->getId());

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            20
        );

        return $this->render('IntegratedPageBundle:page:index.html.twig', [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
        ]);
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
            $channel = $this->getSelectedChannel();

            $page->setChannel($channel);

            $dm = $this->getDocumentManager();

            $dm->persist($page);
            $dm->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
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
            $channel = $this->getSelectedChannel();

            $this->getDocumentManager()->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
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
            $channel = $this->getSelectedChannel();

            $dm = $this->getDocumentManager();

            $dm->remove($page);
            $dm->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return $this->render('IntegratedPageBundle:page:delete.html.twig', [
            'page' => $page,
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
        $channel = $this->getSelectedChannel();

        $form = $this->createForm(
            PageType::class,
            $page,
            [
                'action' => $this->generateUrl('integrated_page_page_new', ['channel' => $channel->getId()]),
                'method' => 'POST',
                'theme' => $this->getTheme($channel),
            ]
        );

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_page_page_index',
            'cancel_route_parameters' => ['channel' => $this->getSelectedChannel()->getId()],
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
        $channel = $page->getChannel();

        $form = $this->createForm(
            PageType::class,
            $page,
            [
                'action' => $this->generateUrl(
                    'integrated_page_page_edit',
                    ['id' => $page->getId(), 'channel' => $channel->getId()]
                ),
                'method' => 'PUT',
                'theme' => $this->getTheme($channel),
            ]
        );

        $form->add('actions', SaveCancelType::class, [
            'cancel_route' => 'integrated_page_page_index',
            'cancel_route_parameters' => ['channel' => $this->getSelectedChannel()->getId()],
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
}
