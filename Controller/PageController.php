<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageController extends Controller
{
    public function showAction(Page $page)
    {
        return $this->render($page->getLayout(), ['page' => $page]);
    }
}
