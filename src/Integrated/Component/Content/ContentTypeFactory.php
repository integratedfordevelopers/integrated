<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Component\Content;

use Integrated\Component\Content\Exception\UnexpectedTypeException;
use Integrated\Component\Content\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeFactory implements ContentTypeFactoryInterface
{


	/**
	 * {@inheritdoc}
	 */
	public function getType($class, $type = null)
	{
		if ($type === null && $class instanceof ContentInterface) {
			$type = $class->getType();
		}

		if ($class instanceof ContentInterface) {
			$class = get_class($class);
		} elseif (!is_string($class)) {
			throw new UnexpectedTypeException($class, 'string or Integrated\\Component\\Content\\ContentInterface');
		} elseif (!class_exists($class) || !is_subclass_of($class, 'Integrated\\Component\\Content\\ContentInterface')) {
			throw new InvalidArgumentException(sprintf('The content class "%s" is not a valid class or not subclass of Integrated\\Component\\Content\\ContentInterface.', $class));
		}

		// todo write code to return a class of ContentTypeInterface
	}
}