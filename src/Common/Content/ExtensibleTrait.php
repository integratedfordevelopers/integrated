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
trait ExtensibleTrait
{
    protected $extensions = null;

    /**
     * Get list of all the extensions names.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        if ($this->extensions === null) {
            $this->extensions = new Registry();
        }

        return $this->extensions;
    }
}
