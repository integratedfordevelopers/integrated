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

use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Component\Form\FormTypeInterface as BaseFormTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface FormTypeInterface extends BaseFormTypeInterface
{
	/**
	 * Returns the content type that the form is based on
	 *
	 * @return ContentTypeInterface the content type
	 */
	public function getType();
} 