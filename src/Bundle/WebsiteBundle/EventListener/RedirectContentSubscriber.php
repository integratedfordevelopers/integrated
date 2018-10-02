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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class RedirectContentSubscriber implements EventSubscriberInterface
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    /**
     * @param DocumentManager     $documentManager
     * @param UrlResolver         $urlResolver
     * @param UrlMatcherInterface $router
     */
    public function __construct(
        ChannelContextInterface $channelContext,
        DocumentManager $documentManager,
        UrlResolver $urlResolver,
        UrlMatcherInterface $matcher
    ) {
        $this->channelContext = $channelContext;
        $this->documentManager = $documentManager;
        $this->urlResolver = $urlResolver;
        $this->matcher = $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $parts = explode('/', $request->getPathInfo());

        if (!$slug = end($parts)) {
            return;
        }

        $document = $this->documentManager->getRepository(Content::class)->findOneBy([
            'slug' => $slug,
            'published' => true,
        ]);

        if (!$document instanceof ContentInterface
            || !$document->isPublished()
            || !$this->channelContext->getChannel()
            || !$document->hasChannel($this->channelContext->getChannel())
        ) {
            return;
        }

        $url = $this->urlResolver->generateUrl($document);

        try {
            $this->matcher->match($url);
        } catch (ExceptionInterface $e) {
            return;
        }

        $event->setResponse(new RedirectResponse($url, 301));
    }
}
