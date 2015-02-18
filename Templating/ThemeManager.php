<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Templating;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeManager // @todo interface
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $themes = [];

    /**
     * @var string
     */
    protected $activeTheme = 'default';

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $id
     * @param array $paths
     * @param array $fallback
     * @return $this
     */
    public function registerTheme($id, array $paths, array $fallback = [])
    {
        if ($this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" already exists', $id));
        }

        $this->themes[$id] = new Theme($id, $paths, $fallback);
        return $this;
    }

    /**
     * @param string $theme
     * @param string $path
     * @return $this
     */
    public function registerPath($theme, $path)
    {
        if ($this->hasTheme($theme)) {
            $this->getTheme($theme)->addPath($path);

        } else {
            $this->registerTheme($theme, [$path]);
        }

        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasTheme($id)
    {
        return isset($this->themes[$id]);
    }

    /**
     * @param string $id
     * @return Theme
     */
    public function getTheme($id)
    {
        if (!$this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" not exists', $id));
        }

        return $this->themes[$id];
    }

    /**
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @return string
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setActiveTheme($id)
    {
        if (!$this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" not exists', $id));
        }

        $this->activeTheme = $id;
        return $this;
    }

    /**
     * @param string $template
     * @param string $theme
     * @return string
     */
    public function locateResource($template, $theme = null)
    {
        $theme = $this->getTheme(null === $theme ? $this->getActiveTheme() : $theme);

        foreach ($theme->getPaths() as $path) {

            if (file_exists($this->kernel->locateResource($path) . '/' . $template)) {
                return $path . '/' . $template;
            }
        }

        foreach ($theme->getFallback() as $fallback) {

            if ($resource = $this->locateResource($template, $fallback)) {
                return $resource;
            }
        }
    }
}
