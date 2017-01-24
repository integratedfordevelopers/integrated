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
     * {@inheritdoc}
     */
    public function supports($outputFormat, \SplFileInfo $image)
    {
        if ($imagick = $this->getImageMagick($image->getPathname())) {
            return true;
        }

        throw FormatException::notSupportedFormat($image->getExtension(), $outputFormat, self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function convert($outputFormat, \SplFileInfo $image)
    {
        if ($imagick = $this->getImageMagick($image->getPathname())) {
            // Make a reasonable path based on the cache path but in a conversion folder
            $file = new \SplFileInfo(sprintf('%s/%s.%s', $image->getPath(), $image->getFilename(), $outputFormat));

            if ($file->isFile()) {
                return $file;
            } else {
                // Attempt conversion
                $imagick->setImageFormat($outputFormat);
                $imagick->writeImage($file);

                // Make sure we'll end up with something
                if ($file->isFile()) {
                    return $file;
                } else {
                    throw RunTimeFormatException::conversionFileCreateFail(self::NAME, $image->getPathname(), $outputFormat);
                }
            }
        }

        throw FormatException::notSupportedFormat($image->getExtension(), $outputFormat, self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function formats()
    {
        if (class_exists('\Imagick')) {
            return new ArrayCollection(\Imagick::queryFormats());
        }

        return new ArrayCollection();
    }

    /**
     * @param string $file
     * @return bool|\Imagick
     */
    protected function getImageMagick($file)
    {
        if (class_exists('\Imagick')) {
            try {
                return new \Imagick($file);
            } catch (\Exception $exception) {
                // Intentionally left blank
            }
        }

        return false;
    }
}
