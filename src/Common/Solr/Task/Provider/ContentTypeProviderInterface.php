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
interface ContentTypeProviderInterface
{
    /**
     * Get a all the content for the given content type id.
     *
     * @param string $id
     *
     * @return Iterator|ContentInterface[]
     */
    public function getContent($id);
}
