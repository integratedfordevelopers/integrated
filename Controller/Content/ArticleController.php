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

use Symfony\Bundle\TwigBundle\TwigEngine;

use Integrated\Bundle\BlockBundle\Templating\BlockRenderer;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Article;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ArticleController
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var BlockRenderer
     */
    protected $blockRenderer;

    /**
     * @param TwigEngine $templating
     * @param ThemeManager $themeManager
     * @param BlockRenderer $blockRenderer
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, BlockRenderer $blockRenderer)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Article $article)
    {
        $this->blockRenderer->setDocument($article);

        return $this->templating->renderResponse($this->themeManager->locateTemplate('content/Article/default.html.twig'), [
            'article' => $article,
        ]);
    }
}
