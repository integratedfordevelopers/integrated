<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Event;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\Content\Extension\Event;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeEvent extends Event
{
	/**
	 * @var ContentTypeInterface
	 */
	private $type;

	public function __construct(ContentTypeInterface $type)
	{
		parent::__construct(self::CONTENT_TYPE);

		$this->type = $type;
	}

	/**
	 * @return ContentTypeInterface
	 */
	public function getType()
	{
		return $this->type;
	}
}