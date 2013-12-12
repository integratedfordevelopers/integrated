<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Driver;

use Symfony\Component\Finder\Finder;

/**
 * FileLocator for directories where YML files can be found
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FileLocator
{
    /**
     * @var array
     */
    protected $directories = array();

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @param array $directories
     */
    public function __construct($directories = array())
    {
        $this->directories = (array) $directories;
    }

    /**
     * @param $extension
     * @return Finder
     */
    public function getFiles($extension)
    {
        if (count($this->directories) > 0) {
            $this->getFinder()
                ->ignoreUnreadableDirs()
                ->in($this->directories)
                ->name("*.$extension")
            ;
        }

        return $this->getFinder();
    }

    /**
     * @param array $directories
     * @return $this
     */
    public function setDirectories($directories)
    {
        $this->directories = $directories;
        return $this;
    }

    /**
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @param Finder $finder
     * @return $this
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
        return $this;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        if (null === $this->finder) {
            $this->finder = new Finder();
        }
        return $this->finder;
    }
}