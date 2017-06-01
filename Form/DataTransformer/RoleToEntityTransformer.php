<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\DataTransformer;

use Integrated\Bundle\UserBundle\Model\Role;
use Integrated\Bundle\UserBundle\Model\RoleManagerInterface;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RoleToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var RoleManagerInterface
     */
    private $manager;

    /**
     * @param RoleManagerInterface $manager
     */
    public function __construct(RoleManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $roles
     * @return array
     */
    public function reverseTransform($roles = [])
    {
        if ($roles) {
            $transformRoles = [];

            foreach ($roles as $role) {
                if ($entity = $this->manager->find($role)) {
                    $transformRoles[] = $entity;
                } else {
                    $roleEntity = new Role($role, $role);
                    $this->manager->persist($roleEntity);

                    $transformRoles[] = $roleEntity;
                }
            }

            return $transformRoles;
        }

        return $roles;
    }

    /**
     * @param array $roles
     * @return array
     */
    public function transform($roles = [])
    {
        if ($roles) {
            $transformRoles = [];
            /** @var Role $role */
            foreach ($roles as $role) {
                $transformRoles[] = $role->getId();
            }

            return $transformRoles;
        }

        return $roles;
    }
}
