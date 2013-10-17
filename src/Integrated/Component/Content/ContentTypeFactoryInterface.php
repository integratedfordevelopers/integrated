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
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentTypeFactoryInterface
{
    /**
	 * Returns the content type.
	 *
	 * If $type is null and $class is a object then the type is extracted from the $class else
	 * a type has to be specified.
	 *
	 * @param string|ContentInterface $class A object or a fully qualified class name of type ContentInterface
	 * @param string                  $type  The content type name
	 *
     * @return ContentTypeInterface
	 *
	 * @throws UnexpectedTypeException  if passed document is not a subclass ContentInterface
	 * @throws InvalidArgumentException if the type does not exist
     */
    public function getType($class, $type = null);
}