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

use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Security\Permission;
use Integrated\Common\Security\Permissions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContentChannelVoter implements VoterInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @param ResolverInterface $resolver
     * @param AccessDecisionManagerInterface $decisionManager
     * @param array $permissions
     */
    public function __construct(
        ResolverInterface $resolver,
        AccessDecisionManagerInterface $decisionManager,
        array $permissions = []
    ) {
        $this->resolver = $resolver;
        $this->decisionManager = $decisionManager;

        $this->permissions = $this->getOptionsResolver()->resolve($permissions);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'view' => Permissions::VIEW,
            'create' => Permissions::CREATE,
            'edit' => Permissions::EDIT,
            'delete' => Permissions::DELETE,
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
    public function vote(TokenInterface $token, $content, array $attributes)
    {
        if (!$content instanceof ChannelableInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_GRANTED;
            $permission = $this->permissions['view'] == $attribute ? Permission::READ : Permission::WRITE;

            foreach ($content->getChannels() as $channel) {
                if (!$this->decisionManager->decide($token, [$permission], $channel)) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }
        }

        return $result;
    }
}
