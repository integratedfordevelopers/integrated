<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Form\DataTransformer;

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class GroupTransformer implements DataTransformerInterface
{
    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    /**
     * @param GroupManagerInterface $groupManager
     */
    public function __construct(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($groupsIds)
    {
        if ($groupsIds === null) {
            return null;
        }

        $groups = [];
        foreach ($groupsIds as $group) {
            if ($group = $this->groupManager->find($group)) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($groups)
    {
        return $groups;
    }
}
