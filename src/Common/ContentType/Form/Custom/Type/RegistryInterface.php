<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Form\Custom\Type;

use Integrated\Common\ContentType\Form\Custom\TypeInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface RegistryInterface
{
    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function add(TypeInterface $type);

    /**
     * @param TypeInterface $type
     * @return bool
     */
    public function has(TypeInterface $type);

    /**
     * @return \ArrayIterator
     */
    public function getIterator();
}
