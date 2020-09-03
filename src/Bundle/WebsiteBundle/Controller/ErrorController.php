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

use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ErrorController extends ExceptionController
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param Environment  $twig
     * @param string       $debug
     * @param ThemeManager $themeManager
     */
    public function __construct(Environment $twig, $debug, ThemeManager $themeManager)
    {
        parent::__construct($twig, $debug);

        $this->themeManager = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate(Request $request, $format, $code, $showException)
    {
        if (!$showException) {
            try {
                if ($template = $this->themeManager->locateTemplate(sprintf('error/%s.%s.twig', $code, $format))) {
                    return $template;
                }
            } catch (CircularFallbackException $e) {
            }
        }

        return parent::findTemplate($request, $format, $code, $showException);
    }
}
