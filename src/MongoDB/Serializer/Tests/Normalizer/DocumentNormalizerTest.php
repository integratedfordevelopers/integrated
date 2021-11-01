<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Serializer\Tests\Normalizer;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\MongoDB\Serializer\Normalizer\DocumentNormalizer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DocumentNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DocumentManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manger;

    /**
     * @var DocumentNormalizer
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->manger = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')->disableOriginalConstructor()->getMock();
        $this->normalizer = new DocumentNormalizer($this->manger);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\NormalizerInterface', $this->normalizer);
        $this->assertInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface', $this->normalizer);
    }

    public function testGetDocumentManager()
    {
        $class = new \ReflectionClass($this->normalizer);

        $method = $class->getMethod('getDocumentManager');
        $method->setAccessible(true);

        $this->assertSame($this->manger, $method->invoke($this->normalizer));
    }

    public function testDenormalize()
    {
        $object = new \stdClass();

        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository->expects($this->once())->method('find')->with($this->identicalTo(['id' => 'data']))->willReturn($object);

        $this->manger->expects($this->once())->method('getRepository')->with($this->identicalTo('class'))->willReturn($repository);

        $this->assertSame($object, $this->normalizer->denormalize(['id' => 'data'], 'class'));
    }

    public function testDenormalizeNotFound()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository->expects($this->once())->method('find')->with($this->identicalTo(['id' => 'data']))->willReturn(null);

        $this->manger->expects($this->once())->method('getRepository')->with($this->identicalTo('class'))->willReturn($repository);

        $this->assertNull($this->normalizer->denormalize(['id' => 'data'], 'class'));
    }

    public function testDenormalizeError()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository->expects($this->once())->method('find')->with($this->identicalTo(['id' => 'data']))->will($this->throwException(new \Exception()));

        $this->manger->expects($this->once())->method('getRepository')->with($this->identicalTo('class'))->willReturn($repository);

        $this->assertNull($this->normalizer->denormalize(['id' => 'data'], 'class'));
    }
}
