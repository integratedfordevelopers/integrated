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
     * @param ResolvedTypeFactoryInterface $factory
     * @return self
     */
    public function setResolvedTypeFactory(ResolvedTypeFactoryInterface $factory);

    /**
     * @param TypeInterface $type
     * @return self
     */
    public function addType(TypeInterface $type);

    /**
     * @param TypeInterface[] $types
     * @return self
     */
    public function addTypes(array $types);

    /**
     * @param TypeExtensionInterface $extension
     * @return self
     */
    public function addTypeExtension(TypeExtensionInterface $extension);

    /**
     * @param TypeExtensionInterface[] $extensions
     * @return self
     */
    public function addTypeExtensions(array $extensions);

    /**
     * @return RegistryInterface
     */
    public function getRegistry();
}
