<?php

/**
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\BlockBundle\Document\Block\TextBlock;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class TextBlockController extends Controller
{
    /**
     * @Template
     */
    public function indexAction(TextBlock $block)
    {
        return [];
    }
}
