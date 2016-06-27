<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Fixtures;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 *
 * @codeCoverageIgnore
 */
class Object2 implements ContentInterface
{
    public function getId()
    {
        return 'id2';
    }

    public function getContentType()
    {
        return 'type2';
    }

    public function setContentType($contentType)
    {
    }
}
