<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\UserBundle\Model\Group;
use Integrated\Bundle\UserBundle\Model\Role;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Common\ContentType\ContentTypeFilterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class WorkflowPermission.
 */
class WorkflowPermission implements ContentTypeFilterInterface
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @param TokenStorage     $tokenStorage
     * @param ObjectRepository $repository
     */
    public function __construct(TokenStorage $tokenStorage, ObjectRepository $repository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->repository = $repository;
    }

    /**
     * @param ContentType $contentType
     *
     * @return bool
     */
    public function hasAccess(ContentType $contentType)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var Role $role */
        foreach ($user->getRoles() as $role) {
            if ($role->getRole() == 'ROLE_ADMIN') {
                return true;
            }
        }

        $workflowId = $contentType->getOption('workflow');

        if ($workflowId) {
            $groups = $user->getGroups();

            /** @var Definition $workflow */
            $workflow = $this->repository->find($workflowId);

            $state = $workflow->getDefault();

            /** @var Definition\Permission $permission */
            foreach ($state->getPermissions() as $permission) {
                /** @var Group $group */
                foreach ($groups as $group) {
                    if ($group->getId() == $permission->getGroup()
                        && $permission->getMask() >= Definition\Permission::WRITE
                    ) {
                        return true;
                    }
                }
                $permission->getGroup();
            }

            return false;
        }

        return true;
    }
}
