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
use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Form\Type\PageCopyType;
use Integrated\Bundle\PageBundle\Form\Type\PageFilterType;
use Integrated\Bundle\PageBundle\Form\Type\PageType;
use Integrated\Bundle\PageBundle\Services\PageCopyService;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    /**
     * @var PageCopyService
     */
    private $pageCopyService;

    /**
     * PageController constructor.
     *
     * @param ChannelContextInterface $channelContext
     * @param PageCopyService         $pageCopyService
     */
    public function __construct(ChannelContextInterface $channelContext, PageCopyService $pageCopyService)
    {
        parent::__construct($channelContext);

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

        $builder = $this->getDocumentManager()->createQueryBuilder($class);

        if ($query = $filterForm->get('q')->getData()) {
            $builder->addOr($builder->expr()->field('title')->equals(new \MongoRegex('/'.$query.'/i')));
            $builder->addOr($builder->expr()->field('path')->equals(new \MongoRegex('/'.$query.'/i')));
        }

        if ($channel = $filterForm->get('channel')->getData()) {
            $builder->field('channel.$id')->equals($channel);
        }

        $builder->sort('path', 1);

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            25
        );

        $response = $this->render('IntegratedPageBundle:page:index.html.twig', [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
            'filterForm' => $filterForm->createView(),
        ]);

        if ($request->query->has('channel')) {
            $response->headers->setCookie(new Cookie('channel', $request->query->get('channel')));
        }

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

        try {
            $channel = $this->getSelectedChannel();

            $builder = $this->getDocumentManager()->createQueryBuilder(Page::class)
                ->field('channel.$id')->equals($channel->getId());

            $pagination = $this->getPaginator()->paginate(
                $builder,
                $request->query->get('page', 1),
                25
            );
        } catch (\RuntimeException $e) {
            $this->get('braincrafted_bootstrap.flash')->error('Please add a website connector for at least one channel to manage pages');

            return $this->render('IntegratedPageBundle:page:error.html.twig');
        }

        if ($formData = $request->request->get('page_copy', null)) {
            $targetChannel = $formData['targetChannel'];
        } else {
            $targetChannel = null;
        }

        $form = $this->createForm(
            PageCopyType::class,
            null,
            [
                'channel' => $channel->getId(),
                'targetChannel' => $targetChannel,
                'action' => $this->generateUrl('integrated_page_page_copy', ['channel' => $channel->getId()]),
                'method' => 'POST',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['action'] != 'refresh') {
                $this->pageCopyService->copyPages($channel->getId(), $form->getData());

                $this->get('braincrafted_bootstrap.flash')->success('Pages copied');

                return $this->redirect($this->generateUrl('integrated_page_page_index', ['channel' => $channel->getId()]));
            }
        }

        return $this->render('IntegratedPageBundle:page:copy.html.twig', [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
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
