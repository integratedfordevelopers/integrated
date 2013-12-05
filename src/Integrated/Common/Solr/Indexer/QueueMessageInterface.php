<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

use ArrayAccess;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueMessageInterface extends ArrayAccess
{
	public function getId();

	public function getType();

	public function getData();
}