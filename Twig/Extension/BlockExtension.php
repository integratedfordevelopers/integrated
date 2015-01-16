<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Twig\Extension;

use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var BlockHandlerRegistryInterface
     */
    private $registry;

    /**
     * @param BlockHandlerRegistryInterface $registry
     */
    public function __construct(BlockHandlerRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @todo: render as sub-request?
     * @param BlockInterface|string $block  Block instance or id
     * @return string
     */
    public function renderBlock($block)
    {
        if (is_string($block)) {
            // @todo: find block by id
        }

        if ($block instanceof BlockInterface) {

            if ($handler = $this->registry->getHandler($block->getType())) {
                return $handler->execute($block);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
