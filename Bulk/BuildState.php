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

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
final class BuildState
{
    /**
     * @var int
     */
    const SELECTED = 0;

    /**
     * @var int
     */
    const CONFIGURED = 1;

    /**
     * @var int
     */
    const CONFIRMED = 2;

    /**
     * @var int
     */
    const EXECUTED = 3;
}