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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface RegistryBuilderInterface
{
    /**
     * Set the resolved type factory.
     *
     * @param ResolvedTypeFactoryInterface $factory
     *
     * @return RegistryBuilderInterface
     */
    public function setResolvedTypeFactory(ResolvedTypeFactoryInterface $factory);

    /**
     * Add the type to the builder.
     *
     * @param TypeInterface $type
     *
     * @return RegistryBuilderInterface
     */
    public function addType(TypeInterface $type);

    /**
     * Add the types to the builder.
     *
     * @param TypeInterface[] $types
     *
     * @return RegistryBuilderInterface
     */
    public function addTypes(array $types);

    /**
     * Add the type extension to the builder.
     *
     * @param TypeExtensionInterface $extension
     */
    public function addTypeExtension(TypeExtensionInterface $extension);

    /**
     * Add the type extensions to the builder.
     *
     * @param TypeExtensionInterface[] $extensions
     *
     * @return RegistryBuilderInterface
     */
    public function addTypeExtensions(array $extensions);

    /**
     * Create a registry from the current builder configuration.
     *
     * @return RegistryInterface
     */
    public function getRegistry();
}
