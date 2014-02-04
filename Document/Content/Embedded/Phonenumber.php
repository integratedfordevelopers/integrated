<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Phonenumber
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Phonenumber
{
	/**
	 * @var string
	 * @ODM\String
	 */
	protected $type;

	/**
	 * @var string
	 * @ODM\String
	 */
    protected $number;

	/**
	 * @param string $number
	 * @return $this
	 */
	public function setNumber($number)
	{
		$this->number = $number === null ? null : (string) $number;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type === null ? null : (string) $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

}