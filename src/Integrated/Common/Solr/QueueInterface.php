<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface QueueInterface extends \Countable
{
	/**
	 * @param QueueMessageInterface $message
	 * @return QueueInterface
	 */
	public function add(QueueMessageInterface $message);

	/**
	 * @param int $limit
	 * @return QueueMessageInterface[]
	 */
	public function get($limit = 1);

	/**
	 * @param QueueMessageInterface $message
	 * @return QueueInterface
	 */
	public function delete(QueueMessageInterface $message);
}