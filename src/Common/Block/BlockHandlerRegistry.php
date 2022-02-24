<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Block;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockHandlerRegistry implements BlockHandlerRegistryInterface
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param string                $type
     * @param BlockHandlerInterface $block
     *
     * @throws InvalidArgumentException
     */
    public function registerHandler($type, BlockHandlerInterface $block)
    {
        if ($this->hasHandler($type)) {
            throw new InvalidArgumentException(sprintf('Block handler "%s" is already registered', $type));
        }

        $this->registry[$type] = $block;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHandler($type)
    {
        return isset($this->registry[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler($type)
    {
        if ($this->hasHandler($type)) {
            return $this->registry[$type];
        }

        return null;
    }
}
