<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentHistoryBundle\Document\Embedded;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class Workflow
{
    /**
     * @var
     */
    protected $state;

    /**
     * @var
     */
    protected $assigned;

    /**
     * @var
     */
    protected $deadline;

    /**
     * @var string
     */
    protected $comment;
}
