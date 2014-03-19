<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension;

use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExtensionEvent extends Event
{
	/**
	 * @var mixed
	 */
	private $data = null;

	/**
	 * @var ContentInterface
	 */
	private $content;

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param ContentInterface $content
	 */
	public function setContent(ContentInterface $content)
	{
		$this->content = $content;
	}

	/**
	 * @return ContentInterface
	 */
	public function getContent()
	{
		return $this->content;
	}
}