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

use Integrated\Common\Security\Permissions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class StaticPermissionVoter implements VoterInterface
{
    /**
     * @var array
     */
    private $permissions;

    /**
     * @var int
     */
    private $decision;

    /**
     * @param int   $decision
     * @param array $permissions
     */
    public function __construct($decision = VoterInterface::ACCESS_GRANTED, array $permissions = [])
    {
        $this->permissions = $this->getOptionsResolver()->resolve($permissions);
        $this->decision = $decision;
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
        return \in_array($attribute, $this->permissions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute)) {
                return $this->decision;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
