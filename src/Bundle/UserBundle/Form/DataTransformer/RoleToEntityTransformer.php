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
use Integrated\Bundle\UserBundle\Model\RoleInterface;
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
     * @param string[] $values
     *
     * @return RoleInterface[]
     */
    public function reverseTransform($values = [])
    {
        if (!\is_array($values)) {
            return [];
        }

        $roles = [];
        $values = array_combine($values, $values);

        foreach ($this->manager->findBy(['role' => $values]) as $role) {
            $roles[] = $role;

            unset($values[$role->getRole()]);
        }

        foreach ($values as $role) {
            $roles[] = $role = new Role($role, $role);

            // <sarcasm>
            //     This is of course the best place to do a import of none existing roles
            // </sarcasm>

            $this->manager->persist($role);
        }

        return $roles;
    }

    /**
     * @param RoleInterface[] $values
     *
     * @return string[]
     */
    public function transform($values = [])
    {
        if (!\is_array($values)) {
            return [];
        }

        $roles = [];

        foreach ($values as $role) {
            if ($role instanceof RoleInterface) {
                $role = $role->getRole();
            }

            $roles[] = (string) $role;
        }

        return array_unique($roles);
    }
}
