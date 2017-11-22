<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Converter\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ImageBundle\Converter\AdapterInterface;
use Integrated\Bundle\ImageBundle\Exception\FormatException;
use Integrated\Bundle\ImageBundle\Exception\RunTimeFormatException;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ImageMagickAdapter implements AdapterInterface
{
    /**
     * @const string
     */
    const NAME = 'Imagick';

    /**
     * @var AppCache
     */
    private $cache;

    /**
     * @param AppCache $cache
     */
    public function __construct(AppCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($outputFormat, StorageInterface $image)
    {
        $file = $this->cache->path($image);

        // Check if've got a video
        if (preg_match('/^video\/(.*)$/', $image->getMetadata()->getMimeType())) {
            // Open the file on the tenth frame, this saves a us a hell of a lot memory
            // When no frame is specified Imagick will write every frame on /tmp
            $imagick = new \Imagick(sprintf('%s[10]', $file->getPathname()));
        } else {
            // Open a we should do with anything that is not video
            $imagick = new \Imagick($file->getPathname());
        }

        // Make a reasonable path based on the cache path but in a conversion folder
        $cache = new \SplFileInfo(sprintf('%s/%s.%s', $file->getPath(), $file->getFilename(), $outputFormat));

        // Check if've got a
        if ($cache->isFile()) {
            return $cache;
        } else {
            // Attempt conversion
            $imagick->setImageFormat($outputFormat);
            $imagick->writeImage($cache->getPathname());

            // Remove any /tmp files created during conversion
            $imagick->clear();

            // Make sure we'll end up with something
            if ($cache->isFile()) {
                // Return the freshly converted file
                return $cache;
            } else {
                // The last resort, this is the last outcome of any the above statements
                throw RunTimeFormatException::conversionFileCreateFail(self::NAME, $image->getPathname(), $outputFormat);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formats()
    {
        if (class_exists('\Imagick')) {
            return new ArrayCollection((new \Imagick())->queryFormats());
        }

        return new ArrayCollection();
    }
}
