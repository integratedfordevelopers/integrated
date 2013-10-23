<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Component\Content\Resolver\MongoDB;

use Doctrine\ODM\MongoDB\Cursor;
use Integrated\Component\Content\ContentTypeIteratorInterface;

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
		if (null === $this->cursor->current() && $this->cursor->valid()) {
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
		$this->cursor->valid();
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