<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Channel\Connector\Adaptor;

use Integrated\Common\Channel\Connector\Adaptor\Registry;
use Integrated\Common\Channel\Connector\AdaptorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Adaptor\\RegistryInterface', $this->getInstance());
    }

    public function testHasAdaptor()
    {
        $registry = $this->getInstance([
            'test1' => $this->getAdaptor(),
            'test2' => $this->getAdaptor()
        ]);

        self::assertTrue($registry->hasAdaptor('test1'));
        self::assertTrue($registry->hasAdaptor('test2'));
        self::assertFalse($registry->hasAdaptor('test3'));
    }

    /**
     * @expectedException \Integrated\Common\Channel\Exception\ExceptionInterface
     */
    public function testHasAdaptorInvalidArgument()
    {
        $this->getInstance()->hasAdaptor(42);
    }

    public function testGetAdaptor()
    {
        $adaptor = $this->getAdaptor();

        self::assertSame($adaptor, $this->getInstance(['test' => $adaptor])->getAdaptor('test'));
    }

    /**
     * @expectedException \Integrated\Common\Channel\Exception\ExceptionInterface
     */
    public function testGetAdaptorInvalidArgument()
    {
        $this->getInstance()->getAdaptor(42);
    }

    /**
     * @expectedException \Integrated\Common\Channel\Exception\ExceptionInterface
     * @expectedExceptionMessage this-is-a-adaptor-that-does-not-exist
     */
    public function testGetAdaptorNotFound()
    {
        $this->getInstance()->getAdaptor('this-is-a-adaptor-that-does-not-exist');
    }

    public function testGetAdaptors()
    {
        $adaptors = [
            'test1' => $this->getAdaptor(),
            'test2' => $this->getAdaptor()
        ];

        self::assertSame($adaptors, $this->getInstance($adaptors)->getAdaptors());
    }

    /**
     * @param AdaptorInterface[] $adaptors
     * @return Registry
     */
    protected function getInstance(array $adaptors = [])
    {
        return new Registry($adaptors);
    }

    /**
     * @return AdaptorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdaptor()
    {
        return $this->getMock('Integrated\\Common\\Channel\\Connector\\AdaptorInterface');
    }
}
