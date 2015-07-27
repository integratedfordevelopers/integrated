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

use Symfony\Component\Finder\Finder;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;

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
     * @return array
     */
    public function getLayouts()
    {
        if (null === $this->layouts) {
            $this->layouts = [];

            foreach ($this->themeManager->getThemes() as $id => $theme) {
                foreach ($theme->getPaths() as $resource) {
                    $path = $this->themeManager->locateResource($resource);

                    if (is_dir($path)) {
                        $finder = new Finder();
                        $finder->files()->in($path)->depth(0)->name('*.html.twig');

                        /** @var \Symfony\Component\Finder\SplFileInfo $file */
                        foreach ($finder as $file) {
                            $this->layouts[] = $resource . '/' . $file->getRelativePathname();
                        }
                    }
                }
            }
        }

        return $this->layouts;
    }
}
