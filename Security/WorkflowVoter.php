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

use Integrated\Bundle\UserBundle\Model\GroupableInterface;

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Definition\Permission;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow;

use Integrated\Common\Content\ContentInterface;

use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Mapping\MetadataInterface;
use Integrated\Common\ContentType\ResolverInterface;

use Integrated\Common\Security\Permissions;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

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
	 * @param array $permissions
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
			'view'   => Permissions::VIEW,
			'create' => Permissions::CREATE,
			'edit'   => Permissions::EDIT,
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
	public function supportsClass($class)
	{
		if (is_object($class)) {
			$class = get_class($class);
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

		// first check if the object has a workflow connected to it and check
		// if the workflow even exists. If any of those condition are negative
		// then the voter wil abstain from voting.

		$class = ClassUtils::getRealClass($object);

		if (!$this->getMetadata($class)->hasOption('workflow')) {
			return VoterInterface::ACCESS_ABSTAIN;
		}

		$type = $this->getContentType($object->getContentType());

		if (!$type || !$type->hasOption('workflow')) {
			return VoterInterface::ACCESS_ABSTAIN;
		}

		$workflow = $this->getWorkflow($type->getOption('workflow'));

		if (!$workflow) {
			return VoterInterface::ACCESS_ABSTAIN;
		}

		// get the current state and verify that it belongs to the workflow. If
		// one or move conditions are negative then pick the default workflow
		// state to work with.

		$state = $this->getState($object);

		if (!$state || $state->getWorkflow() !== $workflow) {
			$state = $workflow->getStates();
			$state = array_shift($state); // there is no default (yet) so pick first one
		}

		if (!$state) {
			// This should not happen as it should not be possible to create
			// a workflow without a state. But check for it anyways as it is
			// technicality possible to have a workflow without a state.

			return VoterInterface::ACCESS_ABSTAIN;
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

		$permissions = $this->getPermissions($token->getUser(), $state);

		// check the permissions: create requires write permission, view
		// requires the read permission, edit and delete required both.

		$result = VoterInterface::ACCESS_ABSTAIN;

		foreach ($attributes as $attribute) {
			if (!$this->supportsAttribute($attribute)) {
				continue;
			}

			$result = VoterInterface::ACCESS_GRANTED;

			if ($this->permissions['view'] == $attribute) {
				if (!$permissions['read']) { return VoterInterface::ACCESS_DENIED; }
			}

			if ($this->permissions['create'] == $attribute) {
				if (!$permissions['write']) { return VoterInterface::ACCESS_DENIED; }
			}

			if ($this->permissions['edit'] == $attribute) {
				if (!$permissions['read'] || !$permissions['write']) { return VoterInterface::ACCESS_DENIED; }
			}

			if ($this->permissions['delete'] == $attribute) {
				if (!$permissions['read'] || !$permissions['write']) { return VoterInterface::ACCESS_DENIED; }
			}
		}

		return $result;
	}

	/**
	 * @param string $type
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
	 * @return MetadataInterface
	 */
	protected function getMetadata($class)
	{
		return $this->metadata->getMetadata($class);
	}

	/**
	 * @param string $id
	 * @return Definition
	 */
	protected function getWorkflow($id)
	{
		$repository = $this->manager->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');
		return $repository->find($id);
	}

	/**
	 * @param ContentInterface $content
	 * @return Definition\State
	 */
	protected function getState(ContentInterface $content)
	{
		$repository = $this->manager->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State');

		if ($result = $repository->findOneBy(['content' => $content])) {
			$result = $result->getState();
		}

		return $result;
	}

	/**
	 * @param GroupableInterface $user
	 * @param Definition\State $state
	 * @return array
	 */
	protected function getPermissions(GroupableInterface $user, Definition\State $state)
	{
		$groups = [];

		foreach ($user->getGroups() as $group)
		{
			$groups[$group->getId()] = $group->getId(); // create lookup table
		}

		$mask = 0;

		if ($groups) {
			$mask_all = Permission::READ | Permission::WRITE;

			foreach ($state->getPermissions() as $permission) {
				if (isset($groups[$permission->getGroup()])) {
					$mask = $mask | ($mask_all & $permission->getMask());

					if ($mask == $mask_all)	{ break; }
				}
			}
		}

		return [
			'read'  => (bool) ($mask & Permission::READ),
			'write' => (bool) ($mask & Permission::WRITE)
		];
	}
}