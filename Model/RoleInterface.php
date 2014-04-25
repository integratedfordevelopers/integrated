<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\Role\RoleInterface as BaseRoleInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RoleInterface extends BaseRoleInterface
{
	public function __construct($role);

	/**
	 * Returns the identity of the role
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Set the label
	 *
	 * @param string $label
	 */
	public function setLabel($label);

	/**
	 * Returns the label of the role
	 *
	 * @return string
	 */
	public function getLabel();

	/**
	 * Set the description
	 *
	 * @param string $description
	 */
	public function setDescription($description);

	/**
	 * Returns a description of the role
	 *
	 * @return string
	 */
	public function getDescription();
}