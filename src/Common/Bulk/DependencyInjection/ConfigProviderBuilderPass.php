<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigProviderBuilderPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $service;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param string $service
     * @param string $tag
     */
    public function __construct($service, $tag)
    {
        $this->service = $service;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->service)) {
            return;
        }

        $builder = $container->getDefinition($this->service);

        foreach ($container->findTaggedServiceIds($this->tag) as $service => $tags) {
            $builder->addMethodCall('addProvider', [$container->getDefinition($service)]);
        }
    }
}
