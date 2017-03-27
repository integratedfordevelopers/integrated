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
use Integrated\Bundle\PageBundle\Form\Type\PageType;
use Integrated\Bundle\PageBundle\Document\Page\Page;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $channel = $this->getSelectedChannel();

        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedPageBundle:Page\Page');
        $builder->field('channel.$id')->equals($channel->getId());

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            20
        );

        return [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $page = new Page();

        $form = $this->createCreateForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $page->setChannel($channel);

            $dm = $this->getDocumentManager();

            $dm->persist($page);
            $dm->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page created');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $this->getDocumentManager()->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template
     *
     * @param Request $request
     * @param Page $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Page $page)
    {
        if ($page->isLocked()) {
            throw $this->createNotFoundException(sprintf('Page "%s" is locked.', $page->getId()));
        }

        $form = $this->createDeleteForm($page->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

            $dm = $this->getDocumentManager();

            $dm->remove($page);
            $dm->flush();

            $this->clearRoutingCache();

            $this->get('braincrafted_bootstrap.flash')->success('Page deleted');

            return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\Form\Form
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
                'theme'  => $this->getTheme($channel),
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
     * @return \Symfony\Component\Form\Form
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
                'theme'  => $this->getTheme($channel),
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
     * @return \Symfony\Component\Form\Form
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
