<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Common\Block\BlockHandlerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
abstract class BlockHandler implements BlockHandlerInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaults(OptionsResolver $resolver)
    {
    }
}