<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task\Provider;

use Integrated\Common\Content\ContentInterface;
use Iterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentProviderInterface
{
    /**
     * Get a all the referenced content for the given id.
     *
     * @param string $id
     *
     * @return Iterator|ContentInterface[]
     */
    public function getReferenced($id);
}
