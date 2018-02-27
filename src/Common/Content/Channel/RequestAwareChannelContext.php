<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Channel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Channel context that will change based on the current request.
 *
 * This context will store the channel in the request object. This will
 * allows separate request to have there own channel. It is not recommended
 * to retrieve the channel directly from the request object as only the
 * channel id is stored.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RequestAwareChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelManagerInterface
     */
    private $manager;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @param ChannelManagerInterface $manager
     * @param RequestStack            $stack
     * @param string                  $attribute
     */
    public function __construct(ChannelManagerInterface $manager, RequestStack $stack, $attribute = '_channel')
    {
        $this->manager = $manager;
        $this->stack = $stack;
        $this->attribute = $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        $request = $this->getRequest();

        if (!$request) {
            return null;
        }

        if (!$request->attributes->has($this->attribute)) {
            return null;
        }

        return $this->manager->find($request->attributes->get($this->attribute));
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel(ChannelInterface $channel = null)
    {
        $request = $this->getRequest();

        if (!$request) {
            return; // no request so can not store the channel
        }

        if ($channel) {
            $request->attributes->set($this->attribute, $channel->getId());
        } else {
            $request->attributes->remove($this->attribute);
        }
    }

    /**
     * Get the current request object.
     */
    protected function getRequest()
    {
        return $this->stack->getCurrentRequest();
    }
}
