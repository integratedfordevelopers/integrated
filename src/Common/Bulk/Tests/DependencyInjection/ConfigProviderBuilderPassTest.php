<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Tests\DependencyInjection;

use Integrated\Common\Bulk\DependencyInjection\ConfigProviderBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigProviderBuilderPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $service1 = $this->createMock(Definition::class);
        $service2 = $this->createMock(Definition::class);

        $definition = $this->createMock(Definition::class);
        $definition->expects($this->exactly(2))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addProvider', $this->identicalTo([$service1])],
                ['addProvider', $this->identicalTo([$service2])]
            );

        $container = $this->getContainer();
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('service')
            ->willReturn(true);

        $container->expects($this->exactly(3))
            ->method('getDefinition')
            ->withConsecutive(['service'], ['tagged.service.1'], ['tagged.service.2'])
            ->willReturnOnConsecutiveCalls($definition, $service1, $service2);

        $container->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('tag')
            ->willReturn([
                'tagged.service.1' => [],
                'tagged.service.2' => [],
            ]);

        $this->getInstance()->process($container);
    }

    public function testProcessServiceNotFound()
    {
        $container = $this->getContainer();
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('service')
            ->willReturn(false);

        $container->expects($this->never())
            ->method('getDefinition');

        $container->expects($this->never())
            ->method('findTaggedServiceIds');

        $this->getInstance()->process($container);
    }

    /**
     * @return ConfigProviderBuilderPass
     */
    protected function getInstance()
    {
        return new ConfigProviderBuilderPass('service', 'tag');
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainer()
    {
        return $this->createMock(ContainerBuilder::class);
    }
}
