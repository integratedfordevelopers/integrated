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

use Integrated\Bundle\ContentBundle\Document\Content\News;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
class NewsController
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
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, News $news)
    {
        $this->blockManager->setDocument($news);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/News/show/' . $page->getLayout()),
            [
                'news' => $news,
                'page' => $page,
            ]
        );
    }
}
