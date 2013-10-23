<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Component\Content\Resolver\MongoDB;

use Integrated\Component\Content\ContentTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentType implements ContentTypeInterface
{
	private $class;

	private $type;

	/**
	 * {@inheritdoc}
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create()
	{
		$instance = new $this->class();
		$instance->setType($this->type);

		return $instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFields()
	{
		// TODO: Implement getFields() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function getField($name)
	{
		// TODO: Implement getField() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasField($name)
	{
		// TODO: Implement hasField() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelations()
	{
		// TODO: Implement getRelations() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRelation($class, $type = null)
	{
		// TODO: Implement getRelation() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasRelation($class, $type = null)
	{
		// TODO: Implement hasRelation() method.
	}
}