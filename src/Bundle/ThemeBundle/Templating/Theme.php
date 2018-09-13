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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Theme
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var array
     */
    protected $fallback = [];

    /**
     * @param $id
     * @param array $paths
     * @param array $fallback
     */
    public function __construct($id, array $paths, array $fallback = [])
    {
        $this->setId($id);
        $this->setPaths($paths);
        $this->setFallback($fallback);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param array $paths
     *
     * @return $this
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function addPath($path)
    {
        if (!\in_array($path, $this->paths)) {
            $this->paths[] = $path;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * @param array $fallback
     *
     * @return $this
     */
    public function setFallback(array $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }
}
