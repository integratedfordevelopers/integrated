<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface BulkHandlerInterface
{
    /**
     * Loops over ContentInterface[] and executes every ActionInterface given.
     *
     * @param ContentInterface[]    $content
     * @param BulkActionInterface[] $actions
     */
    public function execute($content, $actions);
}
