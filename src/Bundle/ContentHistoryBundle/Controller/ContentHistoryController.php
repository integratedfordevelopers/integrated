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

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

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
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @param TwigEngine         $templating
     * @param DocumentRepository $repository
     * @param Paginator          $paginator
     */
    public function __construct(TwigEngine $templating, DocumentRepository $repository, Paginator $paginator)
    {
        $this->templating = $templating;
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * @param Content $content
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Content $content, Request $request)
    {
        $builder = $this->repository->createQueryBuilder();

        $builder->field('contentId')->equals($content->getId());
        $builder->sort('date', 'desc');

        $paginator = $this->paginator->paginate(
            $builder,
            $request->query->get('page', 1),
            $request->query->get('limit', 20)
        );

        return $this->templating->renderResponse('IntegratedContentHistoryBundle:content_history:index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @param ContentHistory $contentHistory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentHistory $contentHistory)
    {
        return $this->templating->renderResponse('IntegratedContentHistoryBundle:content_history:show.html.twig', [
            'contentHistory' => $contentHistory,
        ]);
    }

    /**
     * @param Content $content
     * @param int     $limit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyAction(Content $content, $limit = 3)
    {
        return $this->templating->renderResponse('IntegratedContentHistoryBundle:content_history:history.html.twig', [
            'content' => $content,
            'documents' => $this->repository->findBy(
                ['contentId' => $content->getId()],
                ['date' => 'desc'],
                $limit + 1
            ),
            'limit' => $limit,
        ]);
    }
}
