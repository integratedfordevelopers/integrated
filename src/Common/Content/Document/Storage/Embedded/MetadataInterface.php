<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Document\Storage\Embedded;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface MetadataInterface
{
    /**
     * @return ArrayCollection
     */
    public function storageData();

    /**
     * @return string
     */
    public function getExtension();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return ArrayCollection
     */
    public function getHeaders();

    /**
     * @return ArrayCollection
     */
    public function getMetadata();
}
