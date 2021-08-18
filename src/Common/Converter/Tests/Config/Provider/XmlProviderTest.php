<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config\Provider;

use Integrated\Common\Converter\Config\Provider\XmlProvider;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class XmlProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\TypeProviderInterface', $this->getInstance($this->getFinder()));
    }

    public function testFinderFileExtension()
    {
        /** @var Finder|\PHPUnit_Framework_MockObject_MockObject $finder */
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')->disableOriginalConstructor()->getMock();

        $finder->expects($this->atLeastOnce())
            ->method('files')
            ->willReturnSelf();

        $finder->expects($this->atLeastOnce())
            ->method('name')
            ->with($this->equalTo('*.xml'))
            ->willReturnSelf();

        $this->getInstance($finder);
    }

    public function testGetTypes()
    {
        $provider = $this->getInstance($this->getFinder(['mapping.xml']));

        self::assertContainsTypes(['type 1', 'type 2', 'type 3', 'type 3', 'type 4', 'type 5'], $provider->getTypes('Test\\Class\\Duplicate'));
        self::assertContainsTypes(['type 1', 'type 2', 'type 3'], $provider->getTypes('Test\\Class\\Single'));
    }

    public function testGetTypesLoadOnlyOnce()
    {
        $finder = $this->getFinder();
        $finder->expects($this->once())
            ->method('getIterator');

        $provider = $this->getInstance($finder);

        $provider->getTypes('class');
        $provider->getTypes('class');
    }

    public function testGetTypesNoXml()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $this->getInstance($this->getFinder(['mapping.noxml.xml']))->getTypes('class');
    }

    public function testGetTypesNonExistingFile()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        // This should probably not happen since it would be weird for the finder to return a not
        // existing file. But it is in theory possible so test for it anyways.

        $this->getInstance($this->getFinder(['mapping.does-not-exist.xml']))->getTypes('class');
    }

    public function testGetTypesInvalidXml()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        // $this->getInstance($this->getFinder(['mapping.invalid.xml']))->getTypes('class');
        $this->markTestSkipped('xsd does not exist yet');
    }

    public function testGetTypesMappingMerged()
    {
        $provider = $this->getInstance($this->getFinder(['mapping.merge-one.xml', 'mapping.merge-two.xml']));

        self::assertContainsTypes(['type 1', 'type 1', 'type 2', 'type 2', 'type 3', 'type 3'], $provider->getTypes('Test\\Merge'));
    }

    public function testGetTypesOptions()
    {
        $provider = $this->getInstance($this->getFinder(['mapping.options.xml']));

        self::assertSame([[null], ['string'], [1, 1.1], [false]], $provider->getTypes('Test\\Options\\Array')[0]->getOptions());
        self::assertSame(['string 1', 'string 2', 'string 3'], $provider->getTypes('Test\\Options\\String')[0]->getOptions());
        self::assertSame([1, 2, 3], $provider->getTypes('Test\\Options\\Int')[0]->getOptions());
        self::assertSame([1.1, 2.2, 3.3], $provider->getTypes('Test\\Options\\Float')[0]->getOptions());
        self::assertSame([true, false, true, false, true, false], $provider->getTypes('Test\\Options\\Bool')[0]->getOptions());
        self::assertSame([null, null, null], $provider->getTypes('Test\\Options\\Null')[0]->getOptions());
        self::assertSame([], $provider->getTypes('Test\\Options\\Empty')[0]->getOptions());
    }

    public function testGetTypesOptionKeys()
    {
        $provider = $this->getInstance($this->getFinder(['mapping.keys.xml']));

        self::assertSame([
            'null' => [null],
            'string' => ['string'],
            'number' => [1, 1.1],
            'bool' => [false],
        ], $provider->getTypes('Test\\Keys\\Array')[0]->getOptions());

        self::assertSame([
            'string 1' => 'string 1',
            0 => '0',
            'string 3' => 'string 3',
        ], $provider->getTypes('Test\\Keys\\Mixed')[0]->getOptions());

        self::assertSame([
            'string 2' => 'string 2',
            'string 3' => 'string 3',
        ], $provider->getTypes('Test\\Keys\\Duplicate')[0]->getOptions());

        self::assertSame([1 => '1', 2 => '2', 3 => '3'], $provider->getTypes('Test\\Keys\\Numeric')[0]->getOptions());
    }

    /**
     * @param Finder $finder
     *
     * @return XmlProvider
     */
    protected function getInstance(Finder $finder)
    {
        return new XmlProvider($finder);
    }

    /**
     * Return a Finder which will return a predefined iterator.
     *
     * @param array $files
     *
     * @return Finder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFinder(array $files = [])
    {
        foreach ($files as $index => $value) {
            $files[$index] = new SplFileInfo(__DIR__.'/../../Fixtures/'.$value, '', '');
        }

        $mock = $this->getMockBuilder('Symfony\Component\Finder\Finder')->setMethods(['getIterator'])->getMock();
        $mock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($files));

        return $mock;
    }

    /**
     * Asserts that the two variables are equal.
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    public static function assertContainsTypes($expected, $actual)
    {
        self::assertIsArray($actual);
        self::assertContainsOnlyInstancesOf('Integrated\\Common\\Converter\\Config\\TypeConfigInterface', $actual);

        /** @var TypeConfigInterface $type */
        foreach ($actual as $key => $type) {
            $actual[$key] = $type->getName();
        }

        sort($expected);
        sort($actual);

        self::assertEquals($expected, $actual);
    }
}
