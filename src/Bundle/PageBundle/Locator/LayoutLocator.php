<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Locator;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Symfony\Component\Finder\Finder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LayoutLocator
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var array
     */
    private $layouts;

    /**
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * @param string $theme
     * @param string $directory
     *
     * @return array
     */
    public function getLayouts($theme, $directory = null)
    {
        if (null === $this->layouts) {
            $this->layouts = [];
            foreach ($this->themeManager->getThemes() as $id => $theme2) {
                if ($theme === $id
                    || \in_array($id, $this->themeManager->getTheme($theme)->getFallback())
                    || $id === 'default') {
                    foreach ($theme2->getPaths() as $resource) {
                        $path = $this->themeManager->locateResource($resource).$directory;
                        if (is_dir($path)) {
                            $finder = new Finder();
                            $finder->files()->in($path)->depth(0)->name('*.html.twig');

                            /** @var \Symfony\Component\Finder\SplFileInfo $file */
                            foreach ($finder as $file) {
                                if (!in_array($file->getRelativePathname(), $this->layouts)) {
                                    $this->layouts[] = $file->getRelativePathname();
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->layouts;
    }
}
