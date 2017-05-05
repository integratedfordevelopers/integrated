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
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageController extends Controller
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

        $builder = $this->getDocumentManager()->createQueryBuilder('IntegratedPageBundle:Page\ContentTypePage');
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
     * @param ContentTypePage $page
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
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

            return $this->redirect($this->generateUrl('integrated_page_content_type_page_index', ['channel' => $channel->getId()]));
        }

        return [
            'page' => $page,
            'form' => $form->createView(),
        ];
    }


    /**
     * @param ContentTypePage $page
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm(ContentTypePage $page)
    {
        $channel = $page->getChannel();

        $form = $this->createForm(
            'integrated_page_content_type_page',
            $page,
            [
                'method' => 'PUT',
                'theme'  => $this->getTheme($channel),
                'controller' => $this->get($page->getControllerService()),
            ]
        );

        $form->add('submit', 'submit', ['label' => 'Save']);

        return $form;
    }
}
