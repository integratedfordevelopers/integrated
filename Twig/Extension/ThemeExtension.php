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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
    public function getGlobals()
    {
        return [
            '_theme' => $this->themeManager->getActiveTheme(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_theme_theme';
    }
}
