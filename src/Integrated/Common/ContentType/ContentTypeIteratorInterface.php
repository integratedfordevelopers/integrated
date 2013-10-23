<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Common\ContentType;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentTypeIteratorInterface extends \Iterator
{
	/**
	 * Return the current content type
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return ContentTypeInterface.
	 */
	public function current();

	/**
	 * Move forward to next content type
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next();

	/**
	 * Return the key of the current content type
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key();

	/**
	 * Checks if current position is valid
	 *
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean Returns true on success or false on failure.
	 */
	public function valid();

	/**
	 * Rewind the Iterator to the first content type
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind();
} 