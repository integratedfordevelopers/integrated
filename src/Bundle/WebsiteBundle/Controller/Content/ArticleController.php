<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller\Content;

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\WebsiteBundle\Service\ContentService;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\Error;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ArticleController
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param ContentService $contentService
     * @param TwigEngine $templating
     * @param ThemeManager $themeManager
     */
    public function __construct(ContentService $contentService, TwigEngine $templating, ThemeManager $themeManager)
    {
        $this->contentService = $contentService;
        $this->templating = $templating;
        $this->themeManager = $themeManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Article $article
     *
     * @return Response
     * @throws CircularFallbackException
     * @throws Error
     */
    public function showAction(ContentTypePage $page, Article $article)
    {
        $this->contentService->prepare($article);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/article/show/'.$page->getLayout()),
            [
                'article' => $article,
                'page' => $page,
            ]
        );
    }
}
