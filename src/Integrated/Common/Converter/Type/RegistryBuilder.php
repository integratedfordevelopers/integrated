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
class RegistryBuilder implements RegistryBuilderInterface
{
    /**
     * @var ResolvedTypeFactoryInterface
     */
    private $factory;

    /**
     * @var TypeInterface[]
     */
    private $types = [];

    /**
     * @var TypeExtensionInterface[][]
     */
    private $extensions = [];

    /**
     * {@inheritdoc}
     */
    public function setResolvedTypeFactory(ResolvedTypeFactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addType(TypeInterface $type)
    {
        $this->types[$type->getName()] = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTypes(array $types)
    {
        foreach ($types as $type) {
            $this->addType($type);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeExtension(TypeExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()][] = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addTypeExtension($extension);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistry()
    {
        $registry = [];
        $factory = $this->factory ?: new ResolvedTypeFactory();

        foreach ($this->types as $type) {
            $extensions = [];

            if (isset($this->extensions[$type->getName()])) {
                $extensions = $this->extensions[$type->getName()];
            }

            $registry[$type->getName()] = $factory->createType($type, $extensions);
        }

        return new Registry($registry);
    }
}
