<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Solr\Type;

use ArrayObject;
use DateTime;

use Integrated\Bundle\SolrBundle\Solr\Type\FieldMapperType;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;

use Integrated\Bundle\SolrBundle\Tests\Solr\Type\FieldMapperTypeTestTestObject as TestObject;

/**
 * @covers Integrated\Bundle\SolrBundle\Solr\Type\FieldMapperType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FieldMapperTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    /**
     * @dataProvider buildProvider
     */
    public function testBuild(array $options, array $expected)
    {
        $container = $this->getContainer();
        $object = new TestObject();

        $this->getInstance()->build($container, $object, $options);
        $this->getInstance()->build($container, $object, $options);

        self::assertEquals($expected, $container->toArray());
    }

    public function buildProvider()
    {
        return [
            'simple' => [
                ['field1' => 'field1', 'field2' => 'field2', 'field3' => 'field3', 'field4' => 'field4'],
                ['field1' => ['field1'], 'field2' => ['field2'], 'field3' => ['field3'], 'field4' => ['field4']],
            ],
            'advanced' => [
                [['name' => 'field', '@field1', '@field2', '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'advanced and simple' => [
                ['field' => 'field1', ['name' => 'field', '@field2', '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'advanced double' => [
                [['name' => 'field', '@field1', '@field2'], ['name' => 'field', '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'advanced with out name key' => [
                [['field', '@field1', '@field2', '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'advanced with random keys' => [
                [[2 => 'field', 'ignore' => '@field1', 0 => '@field2', 'x' => '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'advanced with name not at start' => [
                [['@field1', '@field2', 'name' => 'field', '@field3', '@field4']],
                ['field' => ['field1', 'field2', 'field3', 'field4']],
            ],
            'static text' => [
                [['name' => 'field', '@field1', '@field2', 'static text 1', 'static text 2']],
                ['field' => ['field1', 'field2', 'static text 1', 'static text 2']],
            ],
            'array' => [
                [['name' => 'field', ['arrayObject' => []]]],
                ['field' => ['field1', 'field2', 'field3']],
            ],
            'combining fields' => [
                [['name' => 'field', ['arrayObject' => ['@field1', '@field2', '@field3']]]],
                ['field' => ['array1.1 array1.2 array1.3', 'array2.1 array2.2 array2.3']],
            ],
            'combining fields with separator' => [
                [['name' => 'field', ['separator' => '#', 'arrayObject' => ['@field1', '@field2', '@field3']]]],
                ['field' => ['array1.1#array1.2#array1.3', 'array2.1#array2.2#array2.3']],
            ],
            'combining fields advanced 1' => [
                [['name' => 'field', ['@field1', 'arrayObject' => ['@field1', '@field2', '@field3'], 'static text']]],
                ['field' => [
                    'field1 array1.1 array1.2 array1.3 static text', 'field1 array2.1 array2.2 array2.3 static text']
                ],
            ],
            'combining fields advanced 2' => [
                [['name' => 'field', ['@field1', 'arrayObject[array1]' => [], 'static text']]],
                ['field' => [
                    'field1 array1.1 static text', 'field1 array1.2 static text', 'field1 array1.3 static text']
                ],
            ],
            'combining fields with separator advanced 1' => [
                [['name' => 'field', [
                    'separator' => '#',
                    '@field1',
                    'arrayObject' => ['separator' => '', '@field1', '@field2', '@field3'],
                    'static text'
                ]]],
                ['field' => [
                    'field1#array1.1array1.2array1.3#static text',
                    'field1#array2.1array2.2array2.3#static text'
                ]],
            ],
            'combining fields with separator advanced 2' => [
                [['name' => 'field', ['separator' => '#', '@field1', 'arrayObject[array1]' => [], 'static text']]],
                ['field' => [
                    'field1#array1.1#static text',
                    'field1#array1.2#static text',
                    'field1#array1.3#static text'
                ]],
            ],
        ];
    }

    /**
     * @dataProvider buildSpecialOrErrorConditionsProvider
     */
    public function testBuildSpecialOrErrorConditions(array $options, array $expected)
    {
        $container = $this->getContainer();
        $object = new TestObject();

        $this->getInstance()->build($container, $object, $options);

        self::assertEquals($expected, $container->toArray());
    }

    public function buildSpecialOrErrorConditionsProvider()
    {
        return [
            'simple, field does not exist' => [
                ['field1' => 'fieldx', 'field2' => 'fieldx', 'field3' => 'fieldx', 'field4' => 'fieldx'],
                [],
            ],
            'simple, empty path' => [
                ['field1' => '', 'field2' => '', 'field3' => '', 'field4' => ''],
                [],
            ],
            'advanced, field does not exist' => [
                [['name' => 'field', '@fieldx', '@fieldx', '@fieldx', '@fieldx']],
                [],
            ],
            'advanced, empty path' => [
                [['name' => 'field', '@', '@', '@', '@']],
                [],
            ],
            'static text, empty' => [
                [['name' => 'field', '', '', '', '']],
                [],
            ],
            'array, not a array' => [
                [['name' => 'field', ['field1' => []]]],
                ['field' => ['field1']],
            ],
            'array, field does not exist' => [
                [['name' => 'field', ['fieldx' => []]]],
                [],
            ],
        ];
    }

    /**
     * @dataProvider buildStringConversionProvider
     */
    public function testBuildStringConversion(array $options, array $expected)
    {
        $container = $this->getContainer();
        $object = new TestObject();

        $this->getInstance()->build($container, $object, $options);

        self::assertEquals($expected, $container->toArray());
    }

    public function buildStringConversionProvider()
    {
        return [
            'boolean, false' => [
                ['field' => 'bool0'],
                ['field' => ['0']],
            ],
            'boolean, true' => [
                ['field' => 'bool1'],
                ['field' => ['1']],
            ],
            'integer' => [
                ['field' => 'int'],
                ['field' => ['42']],
            ],
            'float' => [
                ['field' => 'float'],
                ['field' => ['4.2']],
            ],
            'datatime' => [
                ['field' => 'datetime'],
                ['field' => ['2013-12-31T23:30:00Z']],
            ],
            'object' => [
                ['field' => 'self'],
                ['field' => ['__toString']],
            ],
            'object no conversion' => [
                ['field' => 'arrayObject'],
                [],
            ],
            'array no conversion' => [
                ['field' => 'array'],
                [],
            ],
            'whitespace' => [
                [['name' => 'field', '  static  text  with  extra  whitespace  ']],
                ['field' => ['static text with extra whitespace']],
            ]
        ];
    }

    public function testGetName()
    {
        self::assertEquals('integrated.fields', $this->getInstance()->getName());
    }

    /**
     * @return FieldMapperType
     */
    protected function getInstance()
    {
        return new FieldMapperType();
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        // Easier to check end result when using an actual container instead of mocking it away. Also
        // the code coverage for the container class is ignored for these tests.

        return new Container();
    }
}
