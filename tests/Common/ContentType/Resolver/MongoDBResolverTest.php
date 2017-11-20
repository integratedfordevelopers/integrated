<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Resolver;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Resolver\MongoDBResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUp()
    {
        $class = $this->getMockClass('Integrated\\Common\\ContentType\\ContentTypeInterface');

        $this->repository = $this->getMockBuilder('Doctrine\\ODM\\MongoDB\\DocumentRepository')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->any())
            ->method('getClassName')
            ->willReturn($class);
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\ContentType\\ResolverInterface', $this->getInstance());
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
     */
    public function testInvalidRepository()
    {
        $this->repository = $this->getMockBuilder('Doctrine\\ODM\\MongoDB\\DocumentRepository')->disableOriginalConstructor()->getMock();
        $this->repository->expects($this->any())
            ->method('getClassName')
            ->willReturn('stdClass');

        $this->getInstance();
    }

    public function testGetType()
    {
        $type = $this->getType();

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($type);

        $resolver = $this->getInstance();

        self::assertSame($type, $resolver->getType('found'));
        self::assertSame($type, $resolver->getType('found')); // second call should return cached version
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\UnexpectedTypeException
     */
    public function testGetTypeNoString()
    {
        $this->getInstance()->getType(['not a string']);
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
     * @expectedExceptionMessage "not found"
     */
    public function testGetTypeNotFound()
    {
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->getInstance()->getType('not found');
    }

    public function testHasType()
    {
        $this->repository->expects($this->at(1))
            ->method('findOneBy')
            ->willReturn($this->getType());

        $this->repository->expects($this->at(2))
            ->method('findOneBy')
            ->willReturn(null);

        $resolver = $this->getInstance();

        self::assertTrue($resolver->hasType('found'));
        self::assertTrue($resolver->hasType('found')); // second call should use cached version
        self::assertFalse($resolver->hasType('not found'));
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\UnexpectedTypeException
     */
    public function testHasTypeNoString()
    {
        $this->getInstance()->hasType(['not a string']);
    }

    public function testGetTypes()
    {
        $types = [
            $this->getType('type 1'),
            $this->getType('type 2'),
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($types);

        $iterator = $this->getInstance()->getTypes();

        self::assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $iterator);
        self::assertSame(['type 1' => $types[0], 'type 2' => $types[1]], iterator_to_array($iterator));
    }

    /**
     * @return MongoDBResolver
     */
    protected function getInstance()
    {
        return new MongoDBResolver($this->repository);
    }

    /**
     * @return ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType($name = null)
    {
        $mock = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');

        if ($name !== null) {
            $mock->expects($this->atLeastOnce())
                ->method('getId')
                ->willReturn($name);
        }

        return $mock;
    }
}
