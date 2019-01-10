<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter;

use Integrated\Common\Converter\Config\ConfigResolverInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Config\Util\ParentAwareConfigIterator;
use Integrated\Common\Converter\Exception\UnexpectedTypeException;
use Integrated\Common\Converter\Type\RegistryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Converter implements ConverterInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var ConfigResolverInterface
     */
    private $resolver;

    /**
     * @var ContainerFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param RegistryInterface         $registry
     * @param ConfigResolverInterface   $resolver
     * @param ContainerFactoryInterface $factory  if no factory is given then a ContainerFactory is created
     *
     * @see ContainerFactory
     */
    public function __construct(RegistryInterface $registry, ConfigResolverInterface $resolver, ContainerFactoryInterface $factory = null)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;

        $this->factory = $factory ?: new ContainerFactory();
    }

    /**
     * {@inheritdoc}
     *
     * @trows UnexpectedTypeException if $data is not a object
     */
    public function convert($data)
    {
        if ($data === null) {
            return $this->factory->createContainer();
        }

        if (!\is_object($data)) {
            throw new UnexpectedTypeException($data, 'object');
        }

        $container = $this->factory->createContainer();

        if ($config = $this->resolver->getConfig(\get_class($data))) {
            /** @var TypeConfigInterface $type */
            foreach (new ParentAwareConfigIterator($config) as $type) {
                $this->registry->getType($type->getName())->build($container, $data, $type->getOptions() ?: []);
            }
        }

        return $container;
    }
}
