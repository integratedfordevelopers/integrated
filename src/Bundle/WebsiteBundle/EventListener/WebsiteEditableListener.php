<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\WebsiteBundle\Service\EditableChecker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class WebsiteEditableListener implements EventSubscriberInterface
{
    /**
     * @var EditableChecker
     */
    protected $websiteEditableChecker;

    /**
     * @var AssetManager
     */
    protected $javascripts;

    /**
     * @param EditableChecker $websiteEditableChecker
     * @param AssetManager    $javascripts
     */
    public function __construct(EditableChecker $websiteEditableChecker, AssetManager $javascripts)
    {
        $this->websiteEditableChecker = $websiteEditableChecker;
        $this->javascripts = $javascripts;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onController'],
        ];
    }

    /**
     * @param ControllerEvent $event
     */
    public function onController(ControllerEvent $event)
    {
        if (!$this->websiteEditableChecker->checkEditable()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->query->get('integrated_website_edit')) {
            // edit mode is off
            return;
        }

        $request->attributes->set('integrated_block_edit', true);
        $request->attributes->set('integrated_menu_edit', true);
    }
}
