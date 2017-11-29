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

use Integrated\Bundle\ContentBundle\Solr\Type\PubActiveType;
use Integrated\Bundle\ContentBundle\Tests\Fixtures\ActiveObject;
use Integrated\Bundle\ContentBundle\Tests\Fixtures\InactiveObject;
use Integrated\Bundle\ContentBundle\Tests\Fixtures\Object1;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;

/**
 * @covers \Integrated\Bundle\ContentBundle\Solr\Type\PubActiveType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class PubActiveTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    /**
     * @dataProvider buildProvider
     */
    public function testBuild(ContentInterface $content, array $expected)
    {
        $container = $this->getContainer();

        $this->getInstance()->build($container, $content);
        $this->getInstance()->build($container, $content); // should clear previous build

        self::assertEquals($expected, $container->toArray());
    }

    public function buildProvider()
    {
        return [
            [
                new ActiveObject(),
                [
                    'pub_active' => [true],
                ],
            ],
            [
                new InactiveObject(),
                [
                    'pub_active' => [false]
                ],
            ]
        ];
    }

    public function testBuildNoContent()
    {
        /* @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->build($container, new Object1());
    }

    public function testGetName()
    {
        self::assertEquals('integrated.pub_active', $this->getInstance()->getName());
    }

    /**
     * @return PubActiveType
     */
    protected function getInstance()
    {
        return new PubActiveType();
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
