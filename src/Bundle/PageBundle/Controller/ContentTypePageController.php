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

use Doctrine\ODM\MongoDB\Query\Builder;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Form\Type\ContentTypePageType;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageController extends Controller
{
    /**
     * PageController constructor.
     *
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        parent::__construct($channelContext);
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

        $channel = $this->getSelectedChannel();

        $builder = $this->getDocumentManager()->createQueryBuilder(ContentTypePage::class)
            ->field('channel.$id')->equals($channel->getId())
            ->sort('contentType');

        $this->displayPathErrors($builder);

        $pagination = $this->getPaginator()->paginate(
            $builder,
            $request->query->get('page', 1),
            25
        );

        return $this->render('IntegratedPageBundle:content_type_page:index.html.twig', [
            'pages' => $pagination,
            'channels' => $this->getChannels(),
            'selectedChannel' => $channel,
        ]);
    }

    /**
     * @param Request         $request
     * @param ContentTypePage $page
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, ContentTypePage $page)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $page->getChannel();

            $this->getDocumentManager()->flush();

            $this->get('integrated_page.services.route_cache')->clear();

            $this->get('braincrafted_bootstrap.flash')->success('Page updated');

            return $this->redirectToRoute('integrated_page_content_type_page_index', ['channel' => $channel->getId()]);
        }

        return $this->render('IntegratedPageBundle:content_type_page:edit.html.twig', [
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
        $channel = $page->getChannel();

        $form = $this->createForm(
            ContentTypePageType::class,
            $page,
            [
                'method' => 'PUT',
                'theme' => $this->getTheme($channel),
                'controller' => $this->get($page->getControllerService()),
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
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
            $settings = $item->getControllerService().$item->getLayout();
            if (isset($paths[$item->getPath()]) && $paths[$item->getPath()] != $settings) {
                $this->get('braincrafted_bootstrap.flash')->error('Path '.$item->getPath().' is used multiple times with diffent settings. Only one will be used');
                continue;
            }
            $paths[$item->getPath()] = $settings;
        }
    }
}
