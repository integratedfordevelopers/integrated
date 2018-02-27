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
use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;
use Integrated\Bundle\WorkflowBundle\Security\WorkflowVoter as BaseWorkflowVoter;

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

    /*
     * Allows this function to be tested separably but also to override the result
     * it need to give to make testing on the permissions easier
     */
    public function getPermissions(GroupableInterface $user, State $state)
    {
        $this->state = $state;

        // permissions will be short circuited if set

        if ($this->permissions === null) {
            return parent::getPermissions($user, $state);
        }

        return $this->permissions;
    }
}
