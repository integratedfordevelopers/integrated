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

use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends AbstractController
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * @param Page $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Page $page)
    {
        return $this->render($this->themeManager->locateTemplate($page->getLayout()), [
            'page' => $page,
        ]);
    }
}
