<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\Serializer\Normalizer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Integrated\MongoDB\Serializer\Normalizer\ContainerAwareDocumentNormalizer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerAwareDocumentNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var ContainerAwareDocumentNormalizer
     */
    private $normalizer;

    protected function setUp()
    {
        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->normalizer = new ContainerAwareDocumentNormalizer($this->container, 'the-service-id');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\NormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface', $this->normalizer);
    }

    public function testGetDocumentManager()
    {
        $manger = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')->disableOriginalConstructor()->getMock();
        $this->container->expects($this->once())->method('get')->with($this->identicalTo('the-service-id'))->will($this->returnValue($manger));

        $class = new \ReflectionClass($this->normalizer);

        $method = $class->getMethod('getDocumentManager');
        $method->setAccessible(true);

        $this->assertSame($manger, $method->invoke($this->normalizer));
        $this->assertSame($manger, $method->invoke($this->normalizer));
    }

    // I don't know what getClassMetadata does if a class can not be found ...
}
