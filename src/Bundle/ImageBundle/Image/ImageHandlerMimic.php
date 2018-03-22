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

use Gregwar\ImageBundle\ImageHandler;

class ImageHandlerMimic extends ImageHandler
{
    /**
     * {@inheritdoc}
     */
    public function save($file, $type = 'svg', $quality = 100)
    {
        try {
            copy($this->source->getFile(), $file);
        } catch (\Exception $e) {
            if ($this->useFallbackImage) {
                return null === $file ? file_get_contents($this->fallback) : $this->getCacheFallback();
            } else {
                throw $e;
            }
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
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->cacheFile('svg');
    }
}
