<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Integrated\Bundle\ContentBundle\Bulk\Action\ActionInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface BulkHandlerInterface
{
    /**
     * Loops over ContentInterface[] and executes every ActionInterface given.
     * @param ContentInterface[] $contents
     * @param ActionInterface[] $actions
     * @return void
     */
    public function execute($contents, $actions);
}
