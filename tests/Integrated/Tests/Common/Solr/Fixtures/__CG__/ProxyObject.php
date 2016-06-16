<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr\Fixtures\__CG__;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 *
 * @codeCoverageIgnore
 */
class ProxyObject implements ContentInterface
{
    public function getId()
    {
        return 'proxy-id';
    }

    public function getContentType()
    {
        return 'proxy-type';
    }

    public function setContentType($contentType)
    {
    }
}
