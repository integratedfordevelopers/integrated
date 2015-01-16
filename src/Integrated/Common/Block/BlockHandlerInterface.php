<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Common\Block;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
interface BlockHandlerInterface
{
    /**
     * Execute block logic
     *
     * @param BlockInterface $block
     * @return Response
     */
    public function execute(BlockInterface $block);
}