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

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
interface BulkActionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getOptions();
}
