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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Bundle\ContentHistoryBundle\History\Parser;
use Integrated\Common\ContentType\ContentTypeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistoryController extends AbstractController
{
    /**
     * @var DocumentRepository
     */
    protected $manager;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var ContentTypeManager
     */
    protected $contentTypeManager;

    /**
     * @param DocumentRepository $repository
     * @param Parser             $parser
     * @param PaginatorInterface $paginator
     */
    public function __construct(DocumentManager $manager, Parser $parser, PaginatorInterface $paginator, ContentTypeManager $contentTypeManager)
    {
        $this->manager = $manager;
        $this->parser = $parser;
        $this->paginator = $paginator;
        $this->contentTypeManager = $contentTypeManager;
    }

    /**
     * @param Content $content
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Content $content, Request $request)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->contentTypeManager->getType($content->getContentType());

        $builder = $this->manager->getRepository(ContentHistory::class)->createQueryBuilder();

        $builder->field('contentId')->equals($content->getId());
        $builder->sort('date', 'desc');

        $paginator = $this->paginator->paginate(
            $builder,
            $request->query->get('page', 1),
            $request->query->get('limit', 20)
        );

        return $this->render('@IntegratedContentHistory/content_history/index.html.twig', [
            'type' => $contentType,
            'content' => $content,
            'paginator' => $paginator,
        ]);
    }

    /**
     * @param ContentHistory $contentHistory
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(ContentHistory $contentHistory)
    {
        $content = $this->manager->find(Content::class, $contentHistory->getContentId());

        /** @var ContentTypeInterface $contentType */
        $contentType = $this->contentTypeManager->getType($content->getContentType());

        return $this->render('@IntegratedContentHistory/content_history/show.html.twig', [
            'type' => $contentType,
            'content' => $content,
            'contentHistory' => $contentHistory,
            'changeSet' => $this->parser->getReadableChangeset($contentHistory),
        ]);
    }

    /**
     * @param Content $content
     * @param int     $limit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function history(Content $content, $limit = 3)
    {
        return $this->render('@IntegratedContentHistory/content_history/history.html.twig', [
            'content' => $content,
            'documents' => $this->manager->getRepository(ContentHistory::class)->findBy(
                ['contentId' => $content->getId()],
                ['date' => 'desc'],
                $limit + 1
            ),
            'limit' => $limit,
        ]);
    }
}
