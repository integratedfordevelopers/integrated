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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentTypeResolverInterface
{
    /**
	 * Returns the content type.
	 *
	 * @param string $class A fully qualified class name of type ContentInterface
	 * @param string $type  The content type name
	 *
     * @return ContentTypeInterface
     */
    public function getType($class, $type);

	/**
	 * check if the content type exists
	 *
	 * @param string $class A fully qualified class name of type ContentInterface
	 * @param string $type  The content type name
	 *
     * @return bool
	 */
	public function hasType($class, $type);
}