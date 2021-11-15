<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SitemapBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RobotsController
{
    /**
     * @return array
     * @Template
     */
    public function index()
    {
        return [];
    }
}
