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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class ExtensionEvents
{
	/**
	 *
	 */
	const POST_READ   = 'extension.read.post';

	/**
	 *
	 */
	const PRE_CREATE  = 'extension.create.pre';

	/**
	 *
	 */
	const POST_CREATE = 'extension.create.post';

	/**
	 *
	 */
	const PRE_UPDATE  = 'extension.update.pre';

	/**
	 *
	 */
	const POST_UPDATE = 'extension.update.post';

	/**
	 *
	 */
	const PRE_DELETE  = 'extension.delete.pre';

	/**
	 *
	 */
	const POST_DELETE = 'extension.delete.post';
}