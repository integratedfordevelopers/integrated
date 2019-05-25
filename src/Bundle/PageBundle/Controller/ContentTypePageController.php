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

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Form\Type\ContentTypePageType;
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
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $channel = $this->getSelectedChannel();

        $builder = $this->getDocumentManager()->createQueryBuilder(ContentTypePage::class)
            ->field('channel.$id')->equals($channel->getId())
            ->sort('contentType');

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
        $form = $this->createEditForm($page);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $this->getSelectedChannel();

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
}
