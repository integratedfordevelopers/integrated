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
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentInterface
{
    /**
	 * Return the type of the Content
	 *
     * @return string
     */
    public function getType();

    /**
     * Set the type of the Content
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);
}