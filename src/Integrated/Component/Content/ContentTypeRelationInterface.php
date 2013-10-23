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
interface ContentTypeRelationInterface
{
//	/**
//	 * Return the name/type op the relation
//	 *
//	 * @return string
//	 */
//	public function getType();

	/**
	 * return the content type relation owner
	 *
	 * @return ContentTypeInterface
	 */
	public function getOwner();

	/**
	 * return the content type relation target
	 *
	 * @return ContentTypeInterface
	 */
	public function getTarget();

	/**
	 * Return the mapping for this relation
	 *
	 * 0 - none
	 * 1 - one
	 * 2 - many
	 *
	 * @see ContentType::MAPPED_NONE
	 * @see ContentType::MAPPED_ONE
	 * @see ContentType::MAPPED_MANY
	 *
	 * @return int
	 */
	public function getMapping();
}