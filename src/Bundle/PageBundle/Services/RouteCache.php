<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Symfony\Component\Finder\Finder;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RouteCache
{
    public const CACHE_PATH_REGEX = '/^app(.*)Url(Matcher|Generator).php/';

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * The routing cache needs to be cleared after a change.
     * This is faster then clearing the cache with the responsible command.
     */
    public function clear()
    {
        $finder = new Finder();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files()->in($this->cacheDir)->depth(0)->name(self::CACHE_PATH_REGEX) as $file) {
            @unlink($file->getRealPath());
        }
    }
}
