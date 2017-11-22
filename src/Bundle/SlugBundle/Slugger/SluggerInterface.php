<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Slugger;

/**
 * Slugger interface
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
interface SluggerInterface
{
    /**
     * @param string $string
     * @param string $delimiter
     *
     * @return string
     */
    public function slugify($string, $delimiter = '-');
}
