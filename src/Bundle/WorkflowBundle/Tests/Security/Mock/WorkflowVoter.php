<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Tests\Security\Mock;

use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Bundle\WorkflowBundle\Security\WorkflowVoter as BaseWorkflowVoter;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowVoter extends BaseWorkflowVoter
{
    /*
     * Store the last used state
     */
    public $state = null;

    /*
     * the permissions that will be returned
     */
    public $permissions = null;

    /**
     * @param ContentInterface $content
     * @param Definition       $workflow
     *
     * @return State
     */
    public function getState(ContentInterface $content, Definition $workflow)
    {
        return $this->state = parent::getState($content, $workflow);
    }

    /*
     * Allows this function to be tested separably but also to override the result
     * it need to give to make testing on the permissions easier
     */
    public function getPermissions(GroupableInterface $user, $permissionGroups)
    {
        // permissions will be short circuited if set

        if ($this->permissions === null) {
            return parent::getPermissions($user, $permissionGroups);
        }

        return $this->permissions;
    }
}
