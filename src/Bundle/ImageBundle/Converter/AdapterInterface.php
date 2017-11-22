<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Converter;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface AdapterInterface
{
    /**
     * Convert a image in another format
     * @param string $outputFormat
     * @param StorageInterface $image
     * @return \SplFileInfo
     */
    public function convert($outputFormat, StorageInterface $image);

    /**
     * @return ArrayCollection
     */
    public function formats();
}
