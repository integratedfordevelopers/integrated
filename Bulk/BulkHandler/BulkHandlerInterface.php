<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\BulkHandler;

use Doctrine\Common\Collections\Collection;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface BulkHandlerInterface
{
    /**
     * @param Collection $contents [ ContentInterface ]
     * @param Collection $actions [ ActionInterface ]
     * @return $this
     */
    public function execute(Collection $contents, Collection $actions);
}
