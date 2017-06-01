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

use Integrated\Bundle\WebsiteBundle\Service\EditableChecker;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WebsiteToolbarListener implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var EditableChecker
     */
    protected $websiteEditableChecker;

    /**
     * @param \Twig_Environment $twig
     * @param EditableChecker $websiteEditableChecker
     */
    public function __construct(\Twig_Environment $twig, EditableChecker $websiteEditableChecker)
    {
        $this->twig = $twig;
        $this->websiteEditableChecker = $websiteEditableChecker;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', -128]];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->websiteEditableChecker->checkEditable()) {
            $this->injectToolbar($event->getResponse());
        }
    }

    /**
     * @param Response $response
     */
    protected function injectToolbar(Response $response)
    {
        $content = $response->getContent();
        $pos = stripos($content, '<body');

        if (false !== $pos) {
            $toolbar = $this->twig->render('IntegratedWebsiteBundle::toolbar.html.twig');

            $end = stripos($content, '>', $pos) + 1;
            $content = substr_replace($content, "\n" . $toolbar, $end, 0);

            $response->setContent($content);
        }
    }
}
