<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Converter\Helper;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ExtensionHelper
{
    /**
     * @param ArrayCollection $collection
     *
     * @return ArrayCollection
     */
    public static function caseTransformBoth(ArrayCollection $collection)
    {
        $extensions = new ArrayCollection();

        foreach ($collection as $extension) {
            if (!$extensions->contains($ext = strtolower($extension))) {
                $extensions->add($ext);
            }

            if (!$extensions->contains($ext = strtoupper($extension))) {
                $extensions->add($ext);
            }

            if (!$extensions->contains($ext = substr(strtolower($extension), 0, 3))) {
                $extensions->add($ext);
            }

            if (!$extensions->contains($ext = substr(strtoupper($extension), 0, 3))) {
                $extensions->add($ext);
            }
        }

        return $extensions;
    }
}
