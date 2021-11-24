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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ErrorController extends AbstractController
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
     * {@inheritdoc}
     */
    public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        try {
            if ($template = $this->themeManager->locateTemplate(sprintf('error/%s.%s.twig', $exception->getStatusCode(), $request->getPreferredFormat()))) {
                return $this->render($template);
            }
        } catch (CircularFallbackException $e) {
        }

        return $this->render($this->themeManager->locateTemplate('error/error.html.twig'));
    }
}
