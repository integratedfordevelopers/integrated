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

use Integrated\Bundle\SolrBundle\Solr\Type\CopyType;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;
use stdClass;

/**
 * @covers \Integrated\Bundle\SolrBundle\Solr\Type\CopyType
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CopyTypeTest extends \PHPUnit_Framework_TestCase
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

        $container->add('field1', 'value1');
        $container->add('field2', 'value2');
        $container->add('field2', 'value3');

        $this->getInstance()->build($container, new stdClass(), $options);
        $this->getInstance()->build($container, new stdClass(), $options);

        self::assertEquals($expected, $container->toArray());
    }

    /**
     * @return array
     */
    public function buildProvider()
    {
        return [
            'simple' => [
                ['field3' => 'field1', 'field4' => 'field2'],
                [
                    'field1' => ['value1'],
                    'field2' => ['value2', 'value3'],
                    'field3' => ['value1'],
                    'field4' => ['value2', 'value3'],
                ],
            ],
            'advanced' => [
                [['name' => 'field3', 'field1', 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
            'advanced and simple' => [
                ['field3' => 'field1', ['name' => 'field3', 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
            'advanced double' => [
                [['name' => 'field3', 'field1'], ['name' => 'field3', 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
            'advanced with out name key' => [
                [['field3', 'field1', 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
            'advanced with random keys' => [
                [[2 => 'field3', 'ignore' => 'field1', 0 => 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
            'advanced with name not at start' => [
                [['field1', 'name' => 'field3', 'field2']],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3'], 'field3' => ['value1', 'value2', 'value3']],
            ],
        ];
    }

    /**
     * @dataProvider buildSpecialOrErrorConditionsProvider
     */
    public function testBuildSpecialOrErrorConditions(array $options, array $expected)
    {
        $container = $this->getContainer();

        $container->add('field1', 'value1');
        $container->add('field2', 'value2');
        $container->add('field2', 'value3');

        $this->getInstance()->build($container, new stdClass(), $options);

        self::assertEquals($expected, $container->toArray());
    }

    /**
     * @return array
     */
    public function buildSpecialOrErrorConditionsProvider()
    {
        return [
            'simple, field does not exist' => [
                ['field3' => 'fieldx', 'field4' => 'fieldx'],
                ['field1' => ['value1'], 'field2' => ['value2', 'value3']],
            ],
            'advanced, field does not exist' => [
                [['name' => 'field3', 'fieldx', 'fieldy']],
                    ['field1' => ['value1'], 'field2' => ['value2', 'value3']],
            ],
        ];
    }

    public function testGetName()
    {
        self::assertEquals('integrated.copy', $this->getInstance()->getName());
    }

    /**
     * @return CopyType
     */
    protected function getInstance()
    {
        return new CopyType();
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
