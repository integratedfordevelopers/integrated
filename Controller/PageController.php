<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\PageBundle\Document\Page\Page;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController
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
     * @var AssetManager
     */
    protected $javascripts;

    /**
     * @param TwigEngine $templating
     * @param ThemeManager $themeManager
     * @param AssetManager $javascrips
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, AssetManager $javascrips)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->javascripts = $javascrips;
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Page $page)
    {
        return $this->templating->renderResponse($this->themeManager->locateTemplate($page->getLayout()), [
            'page' => $page,
            'integrated_block_edit' => false,
            'integrated_menu_edit' => false,
        ]);
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Page $page)
    {
        // @todo security check (INTEGRATED-383)

        $this->javascripts->add('/bundles/integratedwebsite/js/page.js');
        $this->javascripts->add('/bundles/integratedwebsite/js/menu.js');
        $this->javascripts->add('/bundles/integratedwebsite/js/jquery-sortable.js');
        $this->javascripts->add('/bundles/integratedwebsite/js/grid.js');

        return $this->templating->renderResponse($this->themeManager->locateTemplate($page->getLayout()), [
            'page' => $page,
            'integrated_block_edit' => true,
            'integrated_menu_edit' => true,
        ]);
    }
}
