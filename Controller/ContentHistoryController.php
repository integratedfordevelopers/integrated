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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;

use Doctrine\Common\Persistence\ObjectRepository;

use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistoryController extends Controller
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
     * @param TwigEngine $templating
     * @param ObjectRepository $repository
     */
    public function __construct(TwigEngine $templating, ObjectRepository $repository)
    {
        $this->templating = $templating;
        $this->repository = $repository;
    }

    /**
     * @param Content $content
     * @param int $limit
     * @return string
     */
    public function historyAction(Content $content, $limit = 10)
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
