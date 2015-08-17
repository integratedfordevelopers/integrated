<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\StorageBundle\Document\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\StorageBundle\Document\FileTrait;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class File extends Content
{
    use FileTrait;
}
