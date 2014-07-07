<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\DataTransformer;

use ArrayObject;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\Permission;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PermissionTransformer implements DataTransformerInterface
{
	/**
	 * @inheritdoc
	 */
	public function transform($value)
	{
		$data = [
			'read'  => [],
			'write' => []
		];

		if ($value === null || $value === '') {
			return $data;
		}

		if (!is_array($value)) {
			if (!$value instanceof Collection) {
				throw new TransformationFailedException('Expected a Doctrine\\Common\\Collections\\Collection object.');
			}
		}

		foreach ($value as $permission) {
			if (!$permission instanceof Permission) {
				throw new TransformationFailedException('Expected a collection of Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\Permission objects.');
			}

			$group = $permission->getGroup();

			// The object choice list needs a object with a id property.

			if ($permission->hasMask(Permission::READ)) {
				$data['read'][] = new ArrayObject(['id' => $group], ArrayObject::ARRAY_AS_PROPS);
			}

			if ($permission->hasMask(Permission::WRITE)) {
				$data['write'][] = new ArrayObject(['id' => $group], ArrayObject::ARRAY_AS_PROPS);
			}
		}

		return $data;
	}

	/**
	 * @inheritdoc
	 */
	public function reverseTransform($value)
	{
		/** @var Permission[] $permissions */
		$permissions = [];

		if (!isset($value['read']) || $value['read'] === '' || $value['read'] === null) {
			$value['read'] = [];
		}

		if (!$value['read'] instanceof Collection) {
			$value['read'] = new ArrayCollection((array) $value['read']);
		}

		foreach ($value['read'] as $group) {
			$hash = spl_object_hash($group);

			if (!isset($permissions[$hash])) {
				$permissions[$hash] = new Permission();
				$permissions[$hash]->setGroup($group);
			}

			$permissions[$hash]->addMask(Permission::READ);
		}

		if (!isset($value['write']) || $value['write'] === '' || $value['write'] === null) {
			$value['write'] = [];
		}

		if (!$value['write'] instanceof Collection) {
			$value['write'] = new ArrayCollection((array) $value['write']);
		}

		foreach ($value['write'] as $group) {
			$hash = spl_object_hash($group);

			if (!isset($permissions[$hash])) {
				$permissions[$hash] = new Permission();
				$permissions[$hash]->setGroup($group);
			}

			$permissions[$hash]->addMask(Permission::WRITE);
		}

		return new ArrayCollection(array_values($permissions));
	}
} 