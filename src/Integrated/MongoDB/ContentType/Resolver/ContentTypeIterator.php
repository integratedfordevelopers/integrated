<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\ContentType\Resolver;

use Integrated\Common\ContentType\ContentTypeIteratorInterface;
use Doctrine\ODM\MongoDB\Cursor;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeIterator implements ContentTypeIteratorInterface
{
	private $cursor;

	private $current = null;

	/**
	 * @param Cursor $cursor
	 */
	public function __construct(Cursor $cursor)
	{
		$this->cursor  = $cursor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function current()
	{
		if (null === $this->current && $this->cursor->valid()) {
			// lazy load and cache the current content type
			$this->current = $this->cursor->current();
		}

		return $this->current;
	}

	/**
	 * {@inheritdoc}
	 */
	public function next()
	{
		$this->cursor->next();
		$this->current = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function key()
	{
		return $this->cursor->key();
	}

	/**
	 * {@inheritdoc}
	 */
	public function valid()
	{
		return $this->cursor->valid();
	}

	/**
	 * {@inheritdoc}
	 */
	public function rewind()
	{
		$this->cursor->rewind();
		$this->current = null;
	}
}