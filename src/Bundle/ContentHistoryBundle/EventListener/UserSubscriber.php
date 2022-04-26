<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\EventListener;

use Integrated\Bundle\ContentHistoryBundle\Document\Embedded\User;
use Integrated\Bundle\ContentHistoryBundle\Event\ContentHistoryEvent;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ContentHistoryEvent::INSERT => 'onChange',
            ContentHistoryEvent::UPDATE => 'onChange',
            ContentHistoryEvent::DELETE => 'onChange',
        ];
    }

    /**
     * @param ContentHistoryEvent $event
     */
    public function onChange(ContentHistoryEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if ($token instanceof TokenInterface) {
            $user = new User();
            $securityUser = $token->getUser();

            if ($securityUser instanceof UserInterface) {
                $user->setName($securityUser->getUserIdentifier());
            }

            if ($securityUser instanceof \Integrated\Bundle\UserBundle\Model\User) {
                $user->setId($securityUser->getId());

                $relation = $securityUser->getRelation();

                if ($relation instanceof ContentInterface && $name = (string) $relation) {
                    // override with a better name
                    $user->setName($name);
                }
            }

            $event->getContentHistory()->setUser($user);
        }
    }
}
