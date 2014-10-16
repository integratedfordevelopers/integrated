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
class ResolvedType implements ResolvedTypeInterface
{
    /**
     * @var TypeInterface
     */
    private $type;

    /**
     * @var TypeExtensionInterface[]
     */
    private $extensions;

    /**
     * @param TypeInterface $type
     * @param TypeExtensionInterface[] $extensions
     */
    public function __construct($type, array $extensions = [])
    {
        $this->type = $type;
        $this->extensions = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        $this->type->build($container, $data, $options);

        foreach ($this->extensions as $extension) {
            $extension->build($container, $data, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->type->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions()
    {
        return $this->extensions;
    }
}
