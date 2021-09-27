<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\EventListener;

use Integrated\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowDefaultDataListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @param TokenStorageInterface $storage
     */
    public function __construct(TokenStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreData(FormEvent $event)
    {
        if ($event->getData() !== null) {
            return;
        }

        $event->setData([
            'comment' => '',
            'state' => null,
            'assigned' => $this->getUser(),
            'deadline' => null,
        ]);
    }

    /**
     * @return string
     */
    protected function getUser()
    {
        $token = $this->storage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user->getId();
    }
}
