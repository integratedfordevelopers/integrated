<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Mapping;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
interface PropertyInterface
{
    /**
     * @return string
     */
    public function getPropertyName();

    /**
     * @param array $document
     *
     * @return string|null
     */
    public function getFileId(array $document);
}
