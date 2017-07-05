<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Action;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface HandlerInterface
{
    /**
     * @param ContentInterface $content
     */
    public function execute(ContentInterface $content);
}
