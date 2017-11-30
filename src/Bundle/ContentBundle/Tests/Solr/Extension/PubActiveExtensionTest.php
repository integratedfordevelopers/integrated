<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Extension;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Solr\Extension\PubActiveExtension;

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

use stdClass;

/**
 * @covers \Integrated\Bundle\ContentBundle\Solr\Extension\PubActiveExtension
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class PubActiveExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(TypeExtensionInterface::class, $this->getInstance());
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
                $this->getContent(false),
                ['pub_active' => [false]],
            ],
            [
                $this->getContent(true),
                ['pub_active' => [true]],
            ]
        ];
    }

    public function testBuildNoContent()
    {
        /* @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->build($container, new stdClass());
    }

    public function testGetName()
    {
        self::assertEquals('integrated.content', $this->getInstance()->getName());
    }

    /**
     * @return PubActiveExtension
     */
    protected function getInstance()
    {
        return new PubActiveExtension();
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

    /**
     * @param bool $published
     *
     * @return Content|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContent(bool $published)
    {
        $mock = $this->createMock(Content::class);
        $mock->expects($this->atLeastOnce())
            ->method('isPublished')
            ->willReturn($published);

        return $mock;
    }
}
