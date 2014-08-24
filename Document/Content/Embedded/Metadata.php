<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use ArrayIterator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\Content\Registry;

/**
 * Embedded document Metadata
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Metadata extends Registry
{
	/**
	 * @var array
	 * @ODM\Hash
	 */
	protected $data;
}