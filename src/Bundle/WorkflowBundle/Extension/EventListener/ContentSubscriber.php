<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Extension\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\UserBundle\Model\Group;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\Log;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Extension\Event\ContentEvent;
use Integrated\Common\Content\Extension\Event\Subscriber\ContentSubscriberInterface;
use Integrated\Common\Content\Extension\Events;
use Integrated\Common\Content\Extension\ExtensionInterface;
use Integrated\Common\Content\MetadataInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Security\PermissionInterface;
use Integrated\Common\Workflow\Event\WorkflowStateChangedEvent;
use Integrated\Common\Workflow\Events as WorkflowEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentSubscriber implements ContentSubscriberInterface
{
    const CONTENT_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Relation\\Relation';

    /**
     * @var ExtensionInterface
     */
    private $extension;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var ResolverInterface
     */
    private $resolver = null;

    /**
     * @param ExtensionInterface $extension
     * @param ContainerInterface $container
     */
    public function __construct(ExtensionInterface $extension, ContainerInterface $container)
    {
        $this->extension = $extension;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_READ => 'read',
            Events::PRE_CREATE => 'preUpdate',
            Events::POST_CREATE => 'postUpdate',
            Events::PRE_UPDATE => 'preUpdate',
            Events::POST_UPDATE => 'postUpdate',
            Events::POST_DELETE => 'delete',
        ];
    }

    /**
     * @param ContentEvent $event
     */
    public function read(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$this->getWorkflow($content)) {
            return;
        }

        // check if there is a workflow state for this item else just set
        // everything to empty.

        $data = null;

        if ($state = $this->getState($content)) {
            $data = [
                'comment' => '',
                'state' => $state->getState(),
                'assigned' => $state->getAssignedType() == 'user' ? $state->getAssignedId() : null,
                'deadline' => $state->getDeadline(),
            ];
        }

        $event->setData($data);
    }

    /**
     * @param ContentEvent $event
     */
    public function preUpdate(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$workflow = $this->getWorkflow($content)) {
            return;
        }

        $data = \is_array($data = $event->getData()) ? array_filter($data) : []; // filter out empty fields
        $data = $data + [
            'comment' => '',
            'state' => ($state = $this->getState($content)) ? $state->getState() : null,
            'assigned' => null,
            'deadline' => null,
        ];

        // if there still is no state then load / force the default state

        if (!$data['state']) {
            $data['state'] = $workflow->getDefault();
        }

        if (!$data['assigned'] instanceof User && $data['assigned']) {
            $data['assigned'] = $this->container->get('integrated_user.user.manager')->find($data['assigned']);
        }

        if ($data['assigned'] && !$this->hasAssignedAccess($data['assigned'], $data['state'], $content)) {
            $data['assigned'] = null;
        }

        $event->setData($data);

        /**
         * @var Definition\State
         */
        $state = $data['state'];

        if ($content instanceof MetadataInterface) {
            $content->getMetadata()->set('workflow', $state->getWorkflow()->getId());
            $content->getMetadata()->set('workflow_state', $state->getId());
        }

        $content->setDisabled(!$state->isPublishable()); // hax: setDisabled is not in the interface
    }

    /**
     * @param ContentEvent $event
     */
    public function postUpdate(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$this->getWorkflow($content)) {
            return;
        }

        $data = $event->getData();

        if (!$state = $this->getState($content)) {
            $state = new State();
            $state->setContent($content);

            $this->getManager()->persist($state);
        }

        $persist = false;

        $log = new Log();
        $log->setUser($this->getUser());

        if ($data['comment']) {
            $log->setComment($data['comment']);

            $persist = true;
        }

        // log the old settings if changed

        if ($data['state'] !== $state->getState()) {
            $log->setState($data['state']);
            $state->setState($data['state']);

            $dispatcher = $this->container->get('integrated_workflow.event_dispatcher');
            $dispatcher->dispatch(new WorkflowStateChangedEvent($state, $content), WorkflowEvents::STATE_CHANGED);

            $persist = true;
        }

        if ($data['assigned'] !== $state->getAssigned()) {
            $state->setAssigned($data['assigned']);

            //sent mail when user changed

            if ($data['assigned'] instanceof User) {
                if ($data['assigned']->getRelation() instanceof Person) {
                    $person = $data['assigned']->getRelation();

                    if ($person->getEmail()) {
                        $title = 'unknown';
                        if (method_exists($content, 'getTitle')) {
                            $title = $content->getTitle();
                        } elseif (method_exists($content, 'getName')) {
                            $title = $content->getName();
                        }

                        $message = (new \Swift_Message())
                            ->setSubject('[Integrated] "'.$title.'" has been assigned to you')
                            ->setFrom('mailer@integratedforpublishers.com')
                            ->setTo($person->getEmail())
                            ->setBody(
                                'An item has been assigned to you:

Name: '.$title.'
E-mail: '.$person->getEmail().'',
                                'text/plain'
                            );
                        $this->getContainer()->get('mailer')->send($message);
                    }
                }
            }
        }

        if ($data['deadline'] !== $state->getDeadline()) {
            $log->setDeadline($data['deadline']);
            $state->setDeadline($data['deadline']);

            $persist = true;
        }

        if ($persist) {
            $this->getManager()->persist($log);

            $state->addLog($log);
        }

        $this->getManager()->flush($state);
    }

    /**
     * @param ContentEvent $event
     */
    public function delete(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$this->getWorkflow($content)) {
            return;
        }

        if ($state = $this->getState($content)) {
            $this->getManager()->remove($state);
        }

        $event->setData(null);

        if ($content instanceof MetadataInterface) {
            $content->getMetadata()->remove('workflow');
            $content->getMetadata()->remove('workflow_state');
        }
    }

    /**
     * @param ContentInterface $content
     *
     * @return State|null
     */
    protected function getState(ContentInterface $content)
    {
        $repository = $this->getManager()->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State');

        if ($entity = $repository->findOneBy(['content' => $content])) {
            return $entity;
        }

        return null;
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            return null;
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user;
    }

    /**
     * @param object $object
     *
     * @return Definition|null
     */
    protected function getWorkflow($object)
    {
        if (!$object instanceof ContentInterface) {
            return null;
        }

        // resolve the object to a content type and check if it got a workflow connected.

        $type = $object->getContentType();

        if (!$type) {
            return null;
        }

        if (!$this->getResolver()->hasType($type)) {
            return null;
        }

        $type = $this->getResolver()->getType($type);

        if ($workflow = $type->getOption('workflow')) {
            $repository = $this->getManager()->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');

            if ($entity = $repository->find($workflow)) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ObjectManager
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            $this->manager = $this->getContainer()->get('integrated_workflow.extension.doctrine.object_manager');
        }

        return $this->manager;
    }

    /**
     * @return ResolverInterface
     */
    protected function getResolver()
    {
        if ($this->resolver === null) {
            $this->resolver = $this->getContainer()->get('integrated.form.resolver'); // hax
        }

        return $this->resolver;
    }

    /**
     * @param User             $assigned
     * @param Definition\State $state
     * @param ContentInterface $content
     *
     * @return bool
     */
    protected function hasAssignedAccess(User $assigned, Definition\State $state, ContentInterface $content)
    {
        $groups = [];
        /** @var Group $group */
        foreach ($assigned->getGroups() as $group) {
            $groups[] = $group->getId();
        }

        if (\count($state->getPermissions()) > 0) {
            $permissionObject = $state;
        } else {
            //permissions inherited from content type
            $contentType = $this->getContainer()->get('doctrine_mongodb.odm.document_manager')->getRepository(ContentType::class)->find($content->getContentType());
            if ($contentType) {
                if (\count($contentType->getPermissions()) == 0) {
                    return true;
                }
                $permissionObject = $contentType;
            } else {
                return false;
            }
        }

        foreach ($permissionObject->getPermissions() as $permission) {
            if (\in_array($permission->getGroup(), $groups) && $permission->getMask() >= PermissionInterface::WRITE) {
                return true;
            }
        }

        return false;
    }
}
