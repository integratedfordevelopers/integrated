<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\EventListener;

use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ShortCodeListener
{
    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @param BlockManager $blockManager
     */
    public function __construct(BlockManager $blockManager)
    {
        $this->blockManager = $blockManager;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ('Symfony\Component\HttpFoundation\Response' !== \get_class($response)) {
            return;
        }

        $response->setContent(
            preg_replace_callback(
                '/\[block id="(.+?)"(.*?)\]/i',
                [$this, 'replaceWithBlock'],
                $response->getContent()
            )
        );
    }

    /**
     * @param array $matches
     *
     * @return null|string
     */
    public function replaceWithBlock(array $matches)
    {
        return $this->blockManager->render($matches[1]);
    }
}
