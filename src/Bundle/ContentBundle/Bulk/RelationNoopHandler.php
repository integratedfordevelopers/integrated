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

use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RelationNoopHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        // This handler does nothing.
    }
}
