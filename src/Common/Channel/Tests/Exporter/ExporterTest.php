<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Exporter;

use ArrayIterator;
use Exception;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Exporter\ExportableInterface;
use Integrated\Common\Channel\Exporter\Exporter;
use Integrated\Common\Channel\Exporter\ExporterInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExporterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    const TEST_STATE = 'TEST';

    /**
     * @var RegistryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    protected function setUp()
    {
        $this->registry = $this->createMock('Integrated\\Common\\Channel\\Connector\\Adapter\\RegistryInterface');
        $this->resolver = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Exporter\\ExporterInterface', $this->getInstance());
    }

    public function testExport()
    {
        $content = new stdClass();
        $channel = $this->getChannel('channel');

        $exporter1 = $this->getExporter();
        $exporter1->expects($this->exactly(2))
            ->method('export')
            ->with($this->identicalTo($content), $this->equalTo(self::TEST_STATE), $this->identicalTo($channel))
            ->willThrowException(new Exception('i-will-be-caught-and-not-cause-any-troubles'));

        $exporter2 = $this->getExporter();
        $exporter2->expects($this->exactly(2))
            ->method('export')
            ->with($this->identicalTo($content), $this->equalTo(self::TEST_STATE), $this->identicalTo($channel));

        $option1 = $this->getOptions();
        $option2 = $this->getOptions();

        $this->resolver->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([
                $this->getConfig('adapter1', $option1),
                $this->getConfig('adapter2'),
                $this->getConfig('adapter3', $option2),
            ]));

        $this->registry->expects($this->exactly(3))
            ->method('getAdapter')
            ->withConsecutive([$this->equalTo('adapter1')], [$this->equalTo('adapter2')], [$this->equalTo('adapter3')])
            ->willReturnOnConsecutiveCalls($this->getAdapter($option1, $exporter1), $this->getAdapter(), $this->getAdapter($option2, $exporter2));

        $exporter = $this->getInstance();

        $exporter->export($content, self::TEST_STATE, $channel);
        $exporter->export($content, self::TEST_STATE, $channel); // check if the exporters are cached
    }

    public function testExportNoExporters()
    {
        $content = new stdClass();
        $channel = $this->getChannel('channel');

        $this->resolver->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([
                $this->getConfig('adapter1'),
                $this->getConfig('adapter2'),
                $this->getConfig('adapter3'),
            ]));

        $this->registry->expects($this->exactly(3))
            ->method('getAdapter')
            ->withConsecutive([$this->equalTo('adapter1')], [$this->equalTo('adapter2')], [$this->equalTo('adapter3')])
            ->willReturnOnConsecutiveCalls($this->getAdapter(), $this->getAdapter(), $this->getAdapter());

        $exporter = $this->getInstance();

        $exporter->export($content, self::TEST_STATE, $channel);
        $exporter->export($content, self::TEST_STATE, $channel); // check if the exporters are cached
    }

    public function testExportInvalidAdaptor()
    {
        $content = new stdClass();
        $channel = $this->getChannel('channel');

        $this->resolver->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([
                $this->getConfig('adapter1'),
                $this->getConfig('adapter2'),
                $this->getConfig('adapter3'),
            ]));

        $this->registry->expects($this->exactly(3))
            ->method('getAdapter')
            ->withConsecutive([$this->equalTo('adapter1')], [$this->equalTo('adapter2')], [$this->equalTo('adapter3')])
            ->willReturnOnConsecutiveCalls($this->throwException(new Exception('i-will-be-caught-and-not-cause-any-troubles')), $this->getAdapter(), $this->getAdapter());

        $exporter = $this->getInstance();

        $exporter->export($content, self::TEST_STATE, $channel);
        $exporter->export($content, self::TEST_STATE, $channel); // check if the exporters are cached
    }

    public function testExportNoConfig()
    {
        $content = new stdClass();
        $channel = $this->getChannel('channel');

        $this->resolver->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([]));

        $this->registry->expects($this->never())
            ->method($this->anything());

        $exporter = $this->getInstance();

        $exporter->export($content, self::TEST_STATE, $channel);
        $exporter->export($content, self::TEST_STATE, $channel); // check if the exporters are cached
    }

    /**
     * @return Exporter
     */
    protected function getInstance()
    {
        return new Exporter($this->registry, $this->resolver);
    }

    /**
     * @param string $id
     *
     * @return ChannelInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel($id)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }

    /**
     * @return ExporterInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getExporter()
    {
        return $this->createMock('Integrated\\Common\\Channel\\Exporter\\ExporterInterface');
    }

    /**
     * @param string           $adaptor
     * @param OptionsInterface $options
     *
     * @return ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig($adaptor, OptionsInterface $options = null)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ConfigInterface');
        $mock->expects($this->once())
            ->method('getAdapter')
            ->willReturn($adaptor);

        if ($options) {
            $mock->expects($this->once())
                ->method('getOptions')
                ->willReturn($options);
        }

        return $mock;
    }

    /**
     * @return OptionsInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOptions()
    {
        return $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\OptionsInterface');
    }

    /**
     * @param OptionsInterface  $options
     * @param ExporterInterface $exporter
     *
     * @return AdapterInterface | ExportableInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdapter(OptionsInterface $options = null, ExporterInterface $exporter = null)
    {
        if ($options) {
            $mock = $this->createMock('Integrated\\Common\\Channel\\Tests\Fixtures\\ExportableInterface');
            $mock->expects($this->once())
                ->method('getExporter')
                ->with($this->identicalTo($options))
                ->willReturn($exporter);

            return $mock;
        }

        return $this->createMock('Integrated\\Common\\Channel\\Connector\\AdapterInterface');
    }
}
