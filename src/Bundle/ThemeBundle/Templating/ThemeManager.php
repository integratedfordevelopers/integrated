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

use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Twig\Loader\FilesystemLoader;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeManager
{
    /**
     * @var array
     */
    private $themes = [];

    /**
     * @var string
     */
    private $activeTheme = 'default';

    /**
     * @var array
     */
    private $fallbackStack = [];

    /**
     * @var FilesystemLoader
     */
    private $loader;

    public function __construct(FilesystemLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string $id
     * @param array  $paths
     * @param array  $fallback
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function registerTheme($id, array $paths, array $fallback = [])
    {
        if ($this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" already exists.', $id));
        }

        $this->themes[$id] = new Theme($id, $paths, $fallback);

        return $this;
    }

    /**
     * @param string $theme
     * @param string $path
     *
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
     *
     * @return bool
     */
    public function hasTheme($id)
    {
        return isset($this->themes[$id]);
    }

    /**
     * @param string $id
     *
     * @return Theme
     *
     * @throws \InvalidArgumentException
     */
    public function getTheme($id)
    {
        if (!$this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" not exists.', $id));
        }

        return $this->themes[$id];
    }

    /**
     * @return Theme[]
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
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setActiveTheme($id)
    {
        if (!$this->hasTheme($id)) {
            throw new \InvalidArgumentException(sprintf('Theme "%s" not exists.', $id));
        }

        $this->activeTheme = $id;

        return $this;
    }

    /**
     * @param string $template
     * @param string $theme
     *
     * @return string
     *
     * @throws CircularFallbackException
     */
    public function locateTemplate($template, $theme = null)
    {
        // keep BC
        if ('@' == substr($template, 0, 1)) {
            return $template;
        }

        $theme = $this->getTheme(null === $theme ? $this->getActiveTheme() : $theme);

        $this->fallbackStack[$theme->getId()] = 1;

        foreach ($theme->getPaths() as $path) {
            foreach ($this->locateResources($path) as $dir) {
                if (file_exists($dir.'/'.$template)) {
                    $this->fallbackStack = []; // reset

                    return $path.'/'.$template;
                }
            }
        }

        foreach ($theme->getFallback() as $fallback) {
            if (isset($this->fallbackStack[$fallback])) {
                throw CircularFallbackException::templateNotFound(
                    $template,
                    array_merge(array_keys($this->fallbackStack), [$fallback])
                );
            }

            if ($resource = $this->locateTemplate($template, $fallback)) {
                return $resource;
            }
        }

        if (basename($template) !== 'default.html.twig') {
            $secondFallbackTemplate = \dirname($template).'/default.html.twig';
            $this->fallbackStack = [];

            return $this->locateTemplate($secondFallbackTemplate);
        }

        $this->fallbackStack = [];

        return null;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function locateResources($name)
    {
        if (str_starts_with($name, '@')) {
            list($namespace, $path) = explode('/', substr($name, 1), 2);

            $paths = [];
            $namespacePaths = $this->loader->getPaths($namespace);
            if (count($namespacePaths) === 0) {
                throw new \Exception(sprintf('Namespace %s not found. Use Twig namespace notation for themes', $namespace));
            }
            foreach ($namespacePaths as $namespacePath) {
                $paths[] = $namespacePath.'/'.$path;
            }

            return $paths;
        }

        $paths = [];
        foreach ($this->loader->getPaths() as $path) {
            $paths[] = rtrim($path.'/'.trim($name, '/'), '/');
        }

        return $paths;
    }
}
