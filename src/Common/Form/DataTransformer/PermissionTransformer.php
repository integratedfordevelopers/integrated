<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Common\Security\Permission;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PermissionTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var Permission
     */
    protected $permissionClass;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $permissionClass = $this->getPermissionClass();

        $data = [
            'read' => [],
            'write' => [],
        ];

        if ($value === null || $value === '') {
            return $data;
        }

        if (!\is_array($value)) {
            if (!$value instanceof Collection) {
                throw new TransformationFailedException('Expected a Doctrine\\Common\\Collections\\Collection object.');
            }
        }

        foreach ($value as $permission) {
            if (!$permission instanceof $permissionClass) {
                throw new TransformationFailedException('Expected a collection of Integrated\\Common\\Content\\Entity\\Permission objects.');
            }

            $group = $permission->getGroup();
            if (!$group = $this->repository->findOneBy(['id' => $group])) {
                continue;
            }

            // The object choice list needs a object with a id property.

            if ($permission->hasMask($permissionClass::READ)) {
                $data['read'][] = $group;
            }

            if ($permission->hasMask($permissionClass::WRITE)) {
                $data['write'][] = $group;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!\is_array($value)) {
            return new ArrayCollection();
        }

        $permissionClass = $this->getPermissionClass();

        /** @var Permission[] $permissions */
        $permissions = [];

        if (!isset($value['read']) || $value['read'] === '' || $value['read'] === null) {
            $value['read'] = [];
        }

        if (!$value['read'] instanceof Collection) {
            $value['read'] = new ArrayCollection((array) $value['read']);
        }

        foreach ($value['read'] as $group) {
            if (!isset($permissions[$group->getId()])) {
                $permissions[$group->getId()] = new $permissionClass();
                $permissions[$group->getId()]->setGroup($group);
            }

            $permissions[$group->getId()]->addMask($permissionClass::READ);
        }

        if (!isset($value['write']) || $value['write'] === '' || $value['write'] === null) {
            $value['write'] = [];
        }

        if (!$value['write'] instanceof Collection) {
            $value['write'] = new ArrayCollection((array) $value['write']);
        }

        foreach ($value['write'] as $group) {
            if (!isset($permissions[$group->getId()])) {
                $permissions[$group->getId()] = new $permissionClass();
                $permissions[$group->getId()]->setGroup($group);
            }

            $permissions[$group->getId()]->addMask($permissionClass::WRITE);
        }

        return new ArrayCollection(array_values($permissions));
    }

    /**
     * @return string
     */
    protected function getPermissionClass()
    {
        return Permission::class;
    }
}
