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

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Integrated\Bundle\BlockBundle\Templating\BlockRenderer;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ShortCodeListener
{
    /**
     * @var BlockRenderer
     */
    private $blockRenderer;

    /**
     * @param BlockRenderer $blockRenderer
     */
    public function __construct(BlockRenderer $blockRenderer)
    {
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (preg_match('/\[block id="(.+?)"(.*?)\]/i', $response->getContent(), $match)) {

            $response->setContent(str_replace(
                $match[0],
                $this->blockRenderer->render($match[1]),
                $response->getContent()
            ));
        }
    }
}
