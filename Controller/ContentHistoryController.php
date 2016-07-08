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
     * @param TwigEngine $templating
     */
    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param Content $content
     * @return string
     */
    public function historyAction(Content $content)
    {
        return $this->templating->renderResponse('IntegratedContentHistoryBundle:ContentHistory:history.html.twig');
    }
}
