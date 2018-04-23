<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Security;

use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Content\Permission;
use Integrated\Common\ContentType\ResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ChannelVoter implements VoterInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @param ResolverInterface $resolver
     * @param array $permissions
     */
    public function __construct(ResolverInterface $resolver, array $permissions = [])
    {
        $this->resolver = $resolver;
        $this->permissions = $this->getOptionsResolver()->resolve($permissions);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'read' => Permission::READ,
            'write' => Permission::WRITE
        ]);

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->permissions);
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $channel, array $attributes)
    {
        if (!$channel instanceof ChannelInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!count($channel->getPermissions())) {
            return VoterInterface::ACCESS_GRANTED;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        };

        foreach ($user->getRoles() as $role) {
            if ($role == 'ROLE_ADMIN') {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        $permissions = $this->getPermissions($user, $channel);

        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_GRANTED;

            if ($this->permissions['read'] == $attribute) {
                if (!$permissions['read'] && !$permissions['write']) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }

            if ($this->permissions['write'] == $attribute) {
                if (!$permissions['write']) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }
        }

        return $result;
    }

    /**
     * @param GroupableInterface $user
     * @param ChannelInterface $channel
     *
     * @return array
     */
    private function getPermissions(GroupableInterface $user, ChannelInterface $channel)
    {
        $groups = [];

        foreach ($user->getGroups() as $group) {
            $groups[$group->getId()] = $group->getId();
        }

        $mask = 0;

        if ($groups) {
            foreach ($channel->getPermissions() as $permission) {
                if (isset($groups[$permission->getGroup()])) {
                    $mask |= $permission->getMask();
                }
            }
        }

        return [
            'read' => (($mask & Permission::READ) === Permission::READ),
            'write' => (($mask & Permission::WRITE) === Permission::WRITE),
        ];
    }
}
