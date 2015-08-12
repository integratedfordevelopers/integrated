<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Channel\Exporter\Queue;

use Integrated\Common\Channel\Exporter\Queue\ContainerAwareRequestSerializer;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareRequestSerializerTest extends RequestSerializerTest
{
    /**
     * @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $this->container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['serializer', 1, $this->serializer],
                ['manager', 1, $this->manager]
            ]);
    }

    /**
     * @return ContainerAwareRequestSerializer
     */
    protected function getInstance()
    {
        return new ContainerAwareRequestSerializer($this->container, 'serializer', 'manager');
    }
}
