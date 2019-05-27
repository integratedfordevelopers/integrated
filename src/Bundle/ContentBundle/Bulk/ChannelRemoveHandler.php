<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk;

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Bulk\Action\HandlerInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Security\PermissionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ChannelRemoveHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * Constructor.
     *
     * @param string               $channel
     * @param ObjectRepository     $repository
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(string $channel, ObjectRepository $repository, AuthorizationChecker $authorizationChecker)
    {
        $this->channel = $channel;
        $this->repository = $repository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContentInterface $content)
    {
        if (!$content instanceof Content) {
            return;
        }

        $channel = $this->repository->find($this->channel);
        if ($channel !== null) {
            if (!$this->authorizationChecker->isGranted(PermissionInterface::WRITE, $channel)) {
                throw new AccessDeniedException('You are not allowed to write on channel '.$channel->getName());
            }

            /* @var Content $content */
            $content->removeChannel($channel);
        }
    }
}
