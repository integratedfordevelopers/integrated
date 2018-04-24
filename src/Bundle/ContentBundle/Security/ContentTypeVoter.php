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

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Security\PermissionInterface;
use Integrated\Common\Security\Resolver\PermissionResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContentTypeVoter implements VoterInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @param ResolverInterface $resolver
     * @param ObjectRepository $repository
     * @param array $permissions
     */
    public function __construct(ResolverInterface $resolver, ObjectRepository $repository, array $permissions = [])
    {
        $this->resolver = $resolver;
        $this->repository = $repository;
        $this->permissions = $this->getOptionsResolver()->resolve($permissions);
    }

    /**
     * @return OptionsResolver
     */
    protected function getOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'read' => PermissionInterface::READ,
            'write' => PermissionInterface::WRITE
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
    public function vote(TokenInterface $token, $contentType, array $attributes)
    {
        if (!$contentType instanceof ContentTypeInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($user->getRoles() as $role) {
            if ($role == 'ROLE_ADMIN') {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        $workflowId = $contentType->getOption('workflow');
        $permissionGroups = $contentType->getPermissions();

        if ($workflowId) {
            /** @var Definition $workflow */
            $workflow = $this->repository->find($workflowId);
            $state = $workflow->getDefault();

            if (count($state->getPermissions())) {
                // Workflow permissions overrules content type permissions
                $permissionGroups = $state->getPermissions();
            }
        }

        if (!count($permissionGroups)) {
            // No permissions available
            return VoterInterface::ACCESS_GRANTED;
        }

        $permissions = PermissionResolver::getPermissions($user, $permissionGroups);
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_GRANTED;

            if ($this->permissions['read'] == $attribute) {
                if (!$permissions['read']) {
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
}
