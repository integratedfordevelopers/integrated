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

use Gregwar\Image\Source\File;
use Gregwar\ImageBundle\ImageHandler;

class ImageMimicHandler extends ImageHandler
{
    /**
     * {@inheritdoc}
     */
    public function save($file, $type = 'mimic', $quality = 100)
    {
        if ($this->source instanceof File) {
            copy($this->source->getFile(), $file);
        } elseif ($this->useFallbackImage) {
            return null === $file ? file_get_contents($this->fallback) : $this->getCacheFallback();
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function correct()
    {
        return true;
    }

    /**
     * Set a fixed width, because it is not relevant/supported for mimic files.
     */
    public function width()
    {
        return 400;
    }

    /**
     * Image height, because it is not relevant/supported for mimic files.
     */
    public function height()
    {
        return 400;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType()
    {
        if ($this->source instanceof File) {
            return pathinfo($this->source->getFile(), PATHINFO_EXTENSION);
        }

        return parent::guessType();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->cacheFile($this->guessType());
    }
}
