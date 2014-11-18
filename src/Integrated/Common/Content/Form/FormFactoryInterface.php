<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Form;

use Integrated\Common\Content\Exception\UnexpectedTypeException;
use Integrated\Common\Content\ContentInterface;

use Integrated\Common\ContentType\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface FormFactoryInterface
{
	/**
	 * Returns a form type used to create a form.
	 *
	 * @param string|ContentInterface $type A content object or a content type name
	 *
	 * @return FormTypeInterface
	 *
	 * @throws UnexpectedTypeException  if the passed argument is not a string or content object
	 * @throws InvalidArgumentException if the type does not exist
	 */
	public function getType($type);
}
