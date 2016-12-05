<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\Controller;

use Symfony\Bundle\TwigBundle\TwigEngine;

use Doctrine\Common\Persistence\ObjectRepository;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Bundle\ContentHistoryBundle\Form\FormFactory;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistoryController
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @param TwigEngine $templating
     * @param ObjectRepository $repository
     * @param FormFactory $formFactory
     */
    public function __construct(TwigEngine $templating, ObjectRepository $repository, FormFactory $formFactory)
    {
        $this->templating = $templating;
        $this->repository = $repository;
        $this->formFactory = $formFactory;
    }

    /**
     * @param ContentHistory $contentHistory
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentHistory $contentHistory)
    {
        $class = $contentHistory->getContentClass();
        $document = new $class();

        $this->repository->getDocumentManager()->getHydratorFactory()->hydrate(
            $document,
            $contentHistory->getChangeSet()
        );

        $form = $this->formFactory->create($contentHistory->getContentType(), $document);

        return $this->templating->renderResponse('IntegratedContentHistoryBundle:ContentHistory:show.html.twig', [
            'contentHistory' => $contentHistory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Content $content
     * @param int $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyAction(Content $content, $limit = 3)
    {
        return $this->templating->renderResponse('IntegratedContentHistoryBundle:ContentHistory:history.html.twig', [
            'documents' => $this->repository->findBy(
                ['contentId' => $content->getId()],
                ['date' => 'desc'],
                $limit + 1
            ),
            'limit' => $limit,
        ]);
    }
}
