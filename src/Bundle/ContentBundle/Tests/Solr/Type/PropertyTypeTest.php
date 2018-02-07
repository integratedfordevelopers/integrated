<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Type;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Solr\Type\PropertyType;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PropertyTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    /**
     * @dataProvider buildProvider
     *
     * @param ContentInterface $content
     * @param array            $options
     * @param array            $expected
     */
    public function testBuild(ContentInterface $content, array $options, array $expected)
    {
        $container = $this->getContainer();

        $this->getInstance()->build($container, $content, $options);

        self::assertEquals($expected, $container->toArray());
    }

    /**
     * @return array
     */
    public function buildProvider()
    {
        return [
            [$this->getStub(), [['field' => 'contentType', 'fieldValue' => 'type1', 'label' => 'Test 1']], ['facet_properties' => ['Test 1']]],
            [$this->getStub(), [['field' => 'contentType', 'fieldValueNot' => 'type2', 'label' => 'Test 2']], ['facet_properties' => ['Test 2']]],
            [$this->getStub(), [['field' => 'contentType', 'fieldValue' => 'type2', 'label' => 'Test 2']], []],
        ];
    }

    /**
     * @return PropertyType
     */
    protected function getInstance()
    {
        return new PropertyType();
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return new Container();
    }

    /**
     * @return Content
     */
    protected function getStub()
    {
        $stub = $this->createMock(Content::class);
        $stub->method('getContentType')->willReturn('type1');

        return $stub;
    }
}
