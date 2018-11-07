<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Security;

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\UserBundle\Model\GroupableInterface;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\ExtensibleInterface;
use Integrated\Common\Content\Registry;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Integrated\Common\Security\Permission;
use Integrated\Common\Security\Permissions;
use Integrated\Common\Security\Resolver\PermissionResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Acl\Util\ClassUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowVoter implements VoterInterface
{
    /**
     * @var ManagerRegistry
     */
    private $manager;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadata;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @param ManagerRegistry          $manager
     * @param ResolverInterface        $resolver
     * @param MetadataFactoryInterface $metadata
     * @param array                    $permissions
     */
    public function __construct(ManagerRegistry $manager, ResolverInterface $resolver, MetadataFactoryInterface $metadata, array $permissions = [])
    {
        $this->manager = $manager;

        $this->resolver = $resolver;
        $this->metadata = $metadata;

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
        return \in_array($attribute, $this->permissions);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        if (\is_object($class)) {
            $class = \get_class($class);
        }

        return is_subclass_of($class, 'Integrated\\Bundle\\UserBundle\\Model\\GroupableInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$object instanceof ContentInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (\in_array('ROLE_ADMIN', $user->getRoles())) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // first check if the object has a workflow connected to it and check
        // if the workflow even exists. If any of those condition are negative
        // then the voter wil abstain from voting.

        $class = ClassUtils::getRealClass($object);

        if (!$this->getMetadata($class)->hasOption('workflow')) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $contentType = $this->getContentType($object->getContentType());

        if (!$contentType || (!$contentType->hasOption('workflow') && !\count($contentType->getPermissions()))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $permissionGroups = $contentType->getPermissions();

        if ($contentType->hasOption('workflow')) {
            $workflow = $this->getWorkflow($contentType->getOption('workflow'));

            if (!$workflow) {
                return VoterInterface::ACCESS_ABSTAIN;
            }

            // get the current state and verify that it belongs to the workflow. If
            // one or move conditions are negative then pick the default workflow
            // state to work with.

            $state = $this->getState($object, $workflow);

            if (!$state) {
                // This should not happen as it should not be possible to create
                // a workflow without a state. But check for it anyways as it is
                // technicality possible to have a workflow without a state.

                return VoterInterface::ACCESS_ABSTAIN;
            }

            if (\count($state->getPermissions())) {
                // Workflow permissions overrules content type permissions
                $permissionGroups = $state->getPermissions();
            }
        }

        if (!\count($permissionGroups)) {
            // No permissions available
            return VoterInterface::ACCESS_GRANTED;
        }

        // security checks are group based so deny every token class that
        // does not support groups.

        if (!$this->supportsClass($token->getUser())) {
            // if any of the attributes is supported then deny else abstain

            foreach ($attributes as $attribute) {
                if ($this->supportsAttribute($attribute)) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }

            return VoterInterface::ACCESS_ABSTAIN;
        }

        $permissions = $this->getPermissions($token->getUser(), $permissionGroups);
        $isAssigned = $this->isAssigned($token->getUser(), $object);

        // check the permissions: create requires write permission, view
        // requires the read permission, edit and delete required both.

        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_GRANTED;

            if (!$isAssigned) {
                if ($this->permissions['view'] == $attribute) {
                    if (!$permissions['read'] && !$this->isAuthor($token->getUser(), $object)) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }

                if ($this->permissions['create'] == $attribute) {
                    if (!$permissions['write']) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }

                if ($this->permissions['edit'] == $attribute) {
                    if (!$permissions['read'] || !$permissions['write']) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }

                if ($this->permissions['delete'] == $attribute) {
                    if (!$permissions['read'] || !$permissions['write']) {
                        return VoterInterface::ACCESS_DENIED;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $type
     *
     * @return ContentTypeInterface | null
     */
    protected function getContentType($type)
    {
        if ($this->resolver->hasType($type)) {
            return $this->resolver->getType($type);
        }

        return null;
    }

    /**
     * @param $class
     *
     * @return MetadataInterface
     */
    protected function getMetadata($class)
    {
        return $this->metadata->getMetadata($class);
    }

    /**
     * @param string $id
     *
     * @return Definition
     */
    protected function getWorkflow($id)
    {
        $repository = $this->manager->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');

        return $repository->find($id);
    }

    /**
     * @param ContentInterface $content
     * @param Definition       $workflow
     *
     * @return Definition\State
     */
    protected function getState(ContentInterface $content, Definition $workflow)
    {
        $repository = $this->manager->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State');

        if ($result = $repository->findOneBy(['content' => $content])) {
            $result = $result->getState();
        }

        if (!$result || $result->getWorkflow() !== $workflow) {
            $result = $workflow->getStates();
            $result = array_shift($result); // there is no default (yet) so pick first one
        }

        return $result;
    }

    /**
     * @param GroupableInterface $user
     * @param Permission[]       $permissionGroups
     *
     * @return array
     */
    protected function getPermissions(GroupableInterface $user, $permissionGroups)
    {
        return PermissionResolver::getPermissions($user, $permissionGroups);
    }

    /**
     * @param GroupableInterface $user
     * @param ContentInterface   $content
     *
     * @return bool
     */
    protected function isAssigned(GroupableInterface $user, ContentInterface $content)
    {
        if ($content instanceof ExtensibleInterface) {
            /** @var Registry $extensions */
            $extensions = $content->getExtensions();

            $workflowExtension = $extensions->get('integrated.extension.workflow');

            /** @var User $assigned */
            $assigned = $workflowExtension['assigned'];

            if ($assigned instanceof User) {
                $assigned = $assigned->getId();
            }

            if ($assigned && $assigned == $user->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User             $user
     * @param ContentInterface $content
     *
     * @return bool
     */
    protected function isAuthor(User $user, ContentInterface $content)
    {
        if (($userRelation = $user->getRelation()) && $content instanceof Article) {
            /** @var Author $author */
            foreach ($content->getAuthors() as $author) {
                /** @var Person $person */
                $person = $author->getPerson();

                if ($person->getId() == $userRelation->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
