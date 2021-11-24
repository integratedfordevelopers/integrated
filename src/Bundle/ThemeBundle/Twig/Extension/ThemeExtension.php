<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Twig\Extension;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeExtension extends AbstractExtension
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
    public function getFunctions()
    {
        return [
            new TwigFunction('integrated_active_theme', [$this, 'getActiveTheme']),
        ];
    }

    /**
     * @param string $template
     *
     * @return string
     *
     * @throws \Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException
     */
    public function getActiveTheme($template)
    {
        return $this->themeManager->locateTemplate($template);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_theme_theme';
    }
}
