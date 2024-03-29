<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Controller;

use Integrated\Bundle\BlockBundle\Document\Block\InlineTextBlock;
use Integrated\Bundle\BlockBundle\Form\Type\BlockEditType;
use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Johan Liefers@e-active.nl>
 */
class InlineTextBlockController extends BlockController
{
    /**
     * @param Request      $request
     * @param AbstractPage $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, AbstractPage $page)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $block = new InlineTextBlock($page);

        $form = $this->createForm(
            BlockEditType::class,
            $block,
            [
                'method' => 'PUT',
                'data_class' => \get_class($block),
                'type' => $block->getType(),
            ]
        );

        $form->remove('layout');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($block);
            $this->documentManager->flush();

            return $this->render('@IntegratedBlock/block/saved.iframe.html.twig', ['id' => $block->getId()]);
        }

        return $this->render('@IntegratedBlock/block/new.iframe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
