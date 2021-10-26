<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Solr\Extension;

use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;
use Integrated\Common\Security\PermissionInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowExtension implements TypeExtensionInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ObjectRepository
     */
    private $workflow;

    /**
     * @var ObjectRepository
     */
    private $definition;

    /**
     * Constructor.
     *
     * @param ResolverInterface $resolver
     * @param ObjectRepository  $workflow
     * @param ObjectRepository  $definition
     */
    public function __construct(ResolverInterface $resolver, ObjectRepository $workflow, ObjectRepository $definition)
    {
        $this->resolver = $resolver;
        $this->workflow = $workflow;
        $this->definition = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ContentInterface) {
            return; // only process content
        }

        $container->remove('security_workflow_read');
        $container->remove('security_workflow_write');

        if (!$contentType = $this->resolver->getType($data->getContentType())) {
            return; // got no content type
        }

        $permissions = $contentType->getPermissions();
        $state = $this->getState($data);

        if ($state) {
            $container->add('workflow_state', $state->getName());
            $container->add('facet_workflow_state', $state->getName());

            if (\count($state->getPermissions())) {
                // Workflow permissions overrules content type permissions
                $permissions = $state->getPermissions();
            }
        }

        if ($permissions instanceof Collection) {
            $permissions = $permissions->toArray();
        }

        $channelGroups = [];

        if ($data instanceof ChannelableInterface) {
            foreach ($data->getChannels() as $channel) {
                $channelPermissions = $channel->getPermissions();

                if ($channelPermissions instanceof Collection) {
                    $channelPermissions = $channelPermissions->toArray();
                }

                // Need all permissions
                $permissions = array_merge($permissions, $channelPermissions);

                foreach ($channelPermissions as $permission) {
                    // Need channel permissions
                    $channelGroups[$permission->getGroup()] = $permission->getGroup();
                }
            }
        }

        foreach ($permissions as $permission) {
            if (!\count($channelGroups) || isset($channelGroups[$permission->getGroup()])) {
                if ($permission->hasMask(PermissionInterface::READ)) {
                    $container->add('security_workflow_read', $permission->getGroup());
                }

                if ($permission->hasMask(PermissionInterface::WRITE)) {
                    $container->add('security_workflow_write', $permission->getGroup());
                }
            }
        }

        if ($assignee = $this->getAssigned($data)) {
            if ($assignee instanceof User) {
                if ($relation = $assignee->getRelation()) {
                    if ($relation instanceof Person) {
                        $container->add('workflow_assigned', $relation->getFirstname().' '.$relation->getLastname());
                        $container->add('facet_workflow_assigned', $relation->getFirstname().' '.$relation->getLastname());
                    }
                }

                $container->add('workflow_assigned_id', $assignee->getId());
                $container->add('facet_workflow_assigned_id', $assignee->getId());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }

    /**
     * Get the workflow state for the content.
     *
     * If not workflow is connected to the content type or none can be found then null will
     * be returned.
     *
     * @param ContentInterface $content
     */
    protected function getState(ContentInterface $content)
    {
        // does this content even have a workflow connected ?

        $type = $content->getContentType();

        if (!$this->resolver->hasType($type)) {
            return false;
        }

        $type = $this->resolver->getType($type);

        if (!$type->getOption('workflow')) {
            return null;
        }

        // check if there is a state for this content else get the default state for this
        // workflow.

        if ($entity = $this->workflow->findOneBy(['content' => $content])) {
            if ($entity = $entity->getState()) {
                return $entity;
            }

            // seams that the workflow state does not have a definition state connected.
        }

        if ($entity = $this->definition->find($type->getOption('workflow'))) {
            return $entity->getDefault();
        }

        return null;
    }

    /**
     * Get the workflow assignee for the content.
     *
     * @param ContentInterface $content
     */
    protected function getAssigned(ContentInterface $content)
    {
        // does this content even have a workflow connected ?

        $type = $content->getContentType();

        if (!$this->resolver->hasType($type)) {
            return false;
        }

        $type = $this->resolver->getType($type);

        if (!$type->getOption('workflow')) {
            return null;
        }

        // return the assigned instance

        if ($entity = $this->workflow->findOneBy(['content' => $content])) {
            return $entity->getAssigned();
        }

        return null;
    }
}
