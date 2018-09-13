<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\EventListener;

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\PageBundle\Services\ContentTypeControllerManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageListener implements EventSubscriberInterface
{
    /**
     * @var ContentTypeControllerManager
     */
    protected $controllerManager;

    /**
     * @param ContentTypeControllerManager $controllerManager
     */
    public function __construct(ContentTypeControllerManager $controllerManager)
    {
        $this->controllerManager = $controllerManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $contentTypePage = $event->getData();

        if (!$contentTypePage instanceof ContentTypePage) {
            return;
        }

        $className = $contentTypePage->getContentType()->getClass();
        $controller = $this->controllerManager->getController($className);

        if (!\is_array($controller)) {
            throw new \Exception(sprintf('Controller service for class "%s" is not defined', $className));
        }

        $contentTypePage->setControllerService($controller['service']);

        if (\count($controller['controller_actions']) > 1) {
            $event->getForm()->add('controller_action', 'choice', [
                'choices' => array_combine($controller['controller_actions'], $controller['controller_actions']),
            ]);
        } else {
            $contentTypePage->setControllerAction($controller['controller_actions'][0]);
        }
    }
}
