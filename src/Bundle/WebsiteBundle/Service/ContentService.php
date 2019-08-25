<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Service;

use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\WebsiteBundle\EventListener\WebsiteToolbarListener;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Security\Permissions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ContentService
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @var WebsiteToolbarListener
     */
    private $websiteToolbarListener;

    /**
     * @param ChannelContextInterface $channelContext
     * @param AuthorizationChecker    $authorizationChecker
     * @param BlockManager            $blockManager
     * @param WebsiteToolbarListener  $websiteToolbarListener
     */
    public function __construct(ChannelContextInterface $channelContext, AuthorizationChecker $authorizationChecker, BlockManager $blockManager, WebsiteToolbarListener $websiteToolbarListener)
    {
        $this->channelContext = $channelContext;
        $this->authorizationChecker = $authorizationChecker;
        $this->blockManager = $blockManager;
        $this->websiteToolbarListener = $websiteToolbarListener;
    }

    /**
     * @param Content $document
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException
     * @throws \Twig\Error\Error
     */
    public function prepare(Content $content)
    {
        if (!$content->hasChannel($this->channelContext->getChannel())) {
            throw new NotFoundHttpException();
        }

        if ($this->authorizationChecker->isGranted('ROLE_SCOPE_INTEGRATED') && $this->authorizationChecker->isGranted(Permissions::VIEW, $content)) {
            $this->websiteToolbarListener->setContentItem($content);
            if (!$content->isPublished()) {
                $this->websiteToolbarListener->setToolbarMessage('This item is currently unpublished');
            }
        } elseif (!$content->isPublished()) {
            throw new NotFoundHttpException();
        }

        $this->blockManager->setDocument($content);
    }
}
