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
interface ResolvedTypeInterface
{
    /**
     * This build method will first execute the build method of the inner type and then the
     * build methods of all the extensions.
     *
     * @param ContainerInterface $container
     * @param mixed $data
     * @param array $options
     */
    public function build(ContainerInterface $container, $data, array $options = []);

    /**
     * Get the type name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the inner type
     *
     * @return TypeInterface
     */
    public function getType();

    /**
     * Get all the type extensions
     *
     * @return TypeExtensionInterface[]
     */
    public function getTypeExtensions();
}
