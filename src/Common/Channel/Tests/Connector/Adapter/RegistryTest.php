<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Adapter;

use Integrated\Common\Channel\Connector\Adapter\Registry;
use Integrated\Common\Channel\Connector\AdapterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Adapter\\RegistryInterface', $this->getInstance());
    }

    public function testHasAdaptor()
    {
        $registry = $this->getInstance([
            'test1' => $this->getAdapter(),
            'test2' => $this->getAdapter(),
        ]);

        self::assertTrue($registry->hasAdapter('test1'));
        self::assertTrue($registry->hasAdapter('test2'));
        self::assertFalse($registry->hasAdapter('test3'));
    }

    public function testHasAdaptorInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);

        $this->getInstance()->hasAdapter(42);
    }

    public function testGetAdaptor()
    {
        $adaptor = $this->getAdapter();

        self::assertSame($adaptor, $this->getInstance(['test' => $adaptor])->getAdapter('test'));
    }

    public function testGetAdaptorInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);

        $this->getInstance()->getAdapter(42);
    }

    public function testGetAdaptorNotFound()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-adaptor-that-does-not-exist');

        $this->getInstance()->getAdapter('this-is-a-adaptor-that-does-not-exist');
    }

    public function testGetAdaptors()
    {
        $adaptors = [
            'test1' => $this->getAdapter(),
            'test2' => $this->getAdapter(),
        ];

        self::assertSame($adaptors, $this->getInstance($adaptors)->getAdapters());
    }

    /**
     * @param AdapterInterface[] $adaptors
     *
     * @return Registry
     */
    protected function getInstance(array $adaptors = [])
    {
        return new Registry($adaptors);
    }

    /**
     * @return AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdapter()
    {
        return $this->createMock('Integrated\\Common\\Channel\\Connector\\AdapterInterface');
    }
}
