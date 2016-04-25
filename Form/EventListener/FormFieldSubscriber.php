<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Events;

use Integrated\Common\Security\Permissions;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class FormFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BUILD_FIELD => 'buildField'
        ];
    }

    /**
     * @param FieldEvent $event
     */
    public function buildField(FieldEvent $event)
    {
        if ($event->getData() instanceof ContentInterface) {
            $editable = $this->authorizationChecker->isGranted(Permissions::EDIT, $event->getData());

            if (!$editable) {
                $event->getField()->setOptions(
                    array_merge(
                        $event->getField()->getOptions(),
                        ['disabled' => !$editable]
                    )
                );
            }
        }
    }
}