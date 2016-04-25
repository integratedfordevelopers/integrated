<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form\Event;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\ContentType\ContentTypeFieldInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldEvent extends FormEvent
{
	/**
	 * @var ContentTypeFieldInterface
	 */
	private $field = null;

	/**
	 * @var array
	 */
	private $options = [];

	/**
	 * @var bool
	 */
	private $ignore = false;

	/**
	 * @var object|null
	 */
	private $data = null;

	/**
	 * @param ContentTypeFieldInterface $field
	 */
	public function setField(ContentTypeFieldInterface $field)
	{
		$this->field = $field;
	}

	/**
	 * @return null | ContentTypeFieldInterface
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param boolean $ignore
	 */
	public function setIgnore($ignore)
	{
		$this->ignore = (bool) $ignore;
	}

	/**
	 * @return boolean
	 */
	public function isIgnored()
	{
		return $this->ignore;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @return ContentInterface
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param object $data
	 * @return $this
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
}