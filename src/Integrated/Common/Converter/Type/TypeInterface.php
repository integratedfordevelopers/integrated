<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Type;

use Integrated\Common\Converter\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface TypeInterface
{
    /**
     * Extract and manipulated the data from the $data object en add them to the $container.
     *
     * @param ContainerInterface $container
     * @param object             $data
     * @param array              $options
     */
    public function build(ContainerInterface $container, $data, array $options = []);

    /**
     * Get the type name.
     *
     * @return string
     */
    public function getName();
}
