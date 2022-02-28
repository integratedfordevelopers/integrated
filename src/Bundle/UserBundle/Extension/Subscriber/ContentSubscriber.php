<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Extension\Subscriber;

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Integrated\Common\Content\Extension\Event\ContentEvent;
use Integrated\Common\Content\Extension\Event\Subscriber\ContentSubscriberInterface;
use Integrated\Common\Content\Extension\Events;
use Integrated\Common\Content\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentSubscriber implements ContentSubscriberInterface
{
    public const RELATION_CLASS = 'Integrated\\Bundle\\ContentBundle\\Document\\Content\\Relation\\Relation';

    /**
     * @var ExtensionInterface
     */
    private $extension;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManagerInterface
     */
    private $manager;

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
            Events::POST_CREATE => 'update',
            Events::PRE_UPDATE => 'update',
            Events::POST_DELETE => 'delete',
        ];
    }

    public function getExtension()
    {
        return $this->extension;
    }

    protected function isSupported($object)
    {
        $class = new \ReflectionClass($object);

        if (!$class->implementsInterface('Integrated\\Common\\Content\\ContentInterface')) {
            return false;
        }

        if ($class->isSubclassOf(self::RELATION_CLASS)) {
            return true;
        }

        return false;
    }

    public function read(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$this->isSupported($content)) {
            return;
        }

        $manager = $this->getManager();

        if ($user = $manager->findBy(['relation' => $content->getId()])) {
            $event->setData(array_shift($user)); // get first user
        }
    }

    public function update(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$this->isSupported($content)) {
            return;
        }

        if ($user = $event->getData()) {
            $user->setRelation($content);

            $this->getManager()->persist($user);
        }
    }

    public function delete(ContentEvent $event)
    {
        if (!$this->isSupported($event->getContent())) {
            return;
        }

        if ($user = $event->getData()) {
            $this->getManager()->remove($user);
        }

        $event->setData(null);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return UserManagerInterface
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            $this->manager = $this->getContainer()->get('integrated_user.user.manager');
        }

        return $this->manager;
    }
}
