<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface MetadataInterface
{
    /**
     * @return RegistryInterface
     */
    public function getMetadata();

    /**
     * @param RegistryInterface $metadata
     * @return self
     */
    public function setMetadata(RegistryInterface $metadata);
}
