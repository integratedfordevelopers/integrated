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

use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\WebsiteBundle\Service\ContentService;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class TaxonomyController
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
     * @param TwigEngine     $templating
     * @param ThemeManager   $themeManager
     */
    public function __construct(ContentService $contentService, TwigEngine $templating, ThemeManager $themeManager)
    {
        $this->contentService = $contentService;
        $this->templating = $templating;
        $this->themeManager = $themeManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Taxonomy        $taxonomy
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, Taxonomy $taxonomy)
    {
        $this->contentService->prepare($taxonomy);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/taxonomy/show/'.$page->getLayout()),
            [
                'taxonomy' => $taxonomy,
                'page' => $page,
            ]
        );
    }
}
