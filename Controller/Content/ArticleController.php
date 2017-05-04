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

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\BlockBundle\Templating\BlockManager;
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
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @param TwigEngine $templating
     * @param ThemeManager $themeManager
     * @param BlockManager $blockManager
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, BlockManager $blockManager)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->blockManager = $blockManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, Article $article)
    {
        $this->blockManager->setDocument($article);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/Article/show/' . $page->getLayout()),
            [
                'article' => $article,
                'page' => $page,
            ]
        );
    }
}
