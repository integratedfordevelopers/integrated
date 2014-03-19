<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Extension\Adaptor;

use Integrated\Common\Content\Extension\ExtensionAdaptorInterface;
use Integrated\Common\Content\Extension\ExtensionDispatcherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
abstract class AbstractAdaptor implements ExtensionAdaptorInterface
{
	/**
	 * @var ExtensionDispatcherInterface | null
	 */
	protected $dispatcher = null;

	public function setDispatcher(ExtensionDispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	public function getDispatcher()
	{
		return $this->dispatcher;
	}
} 