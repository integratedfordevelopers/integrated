<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Image;

use Gregwar\Cache\CacheInterface;

class ImageMimic extends \Gregwar\Image\Image
{
    protected $originalFile;

    /**
     * {@inheritdoc}
     */
    public function __construct($originalFile = null, $width = null, $height = null)
    {
        parent::__construct($originalFile, $width, $height);

        $this->originalFile = $originalFile;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheSystem()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheSystem(CacheInterface $cache)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheDir($cacheDir)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheDirMode($dirMode)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setForceCache($forceCache = true)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setActualCacheDir($actualCacheDir)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrettyName($name, $prefix = true)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function urlize($name)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setResource($resource)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function useFallback($useFallbackImage = true)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFallback($fallback = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFallback()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheFallback()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdapter($adapter)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePath()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fromFile($originalFile)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function correct()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addOperation($method, $args)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($methodName, $args)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeOperations()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generateHash($type = 'guess', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash($type = 'guess', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cacheFile($type = 'jpg', $quality = 80, $actual = false)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cacheData($type = 'jpg', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilename($filename)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jpeg($quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function gif()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function png()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function guess($quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applyOperations()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save($file, $type = 'guess', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($type = 'guess', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function width()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function height()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function html($title = '', $type = 'jpg', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function inline($type = 'jpg', $quality = 80)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function open($file = '')
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    public static function create($width, $height)
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    public static function fromData($data)
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    public static function fromResource($resource)
    {
        return new static();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->originalFile;
    }
}
