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
use Integrated\Bundle\ContentBundle\Solr\Extension\ContentTypeExtension;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

use stdClass;

/**
 * @covers \Integrated\Bundle\ContentBundle\Solr\Extension\ContentTypeExtension
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(TypeExtensionInterface::class, $this->getInstance($this->getResolver()));
    }

    /**
     * @param string $type
     * @param string $name
     * @param array $expected
     *
     * @dataProvider buildProvider
     */
    public function testBuild(string $type, string $name, array $expected)
    {
        $container = $this->getContainer();


        $this->getInstance($this->getResolver($type, $this->getContentType($name)))->build($container, $this->getContent($type));
        $this->getInstance($this->getResolver($type, $this->getContentType($name)))->build($container, $this->getContent($type)); // should clear previous build

        self::assertEquals($expected, $container->toArray());
    }

    public function buildProvider()
    {
        return [
            [
                'news',
                'News',
                [
                    'facet_contenttype' => ['News']
                ]
            ],
            [
                'article',
                'Blog',
                [
                    'facet_contenttype' => ['Blog']
                ]
            ],
        ];
    }

    public function testBuildNoContent()
    {
        /* @var ContainerInterface | \PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->never())
            ->method($this->anything());

        $this->getInstance($this->getResolver())->build($container, new stdClass());
    }

    public function testGetName()
    {
        self::assertEquals('integrated.content', $this->getInstance($this->getResolver())->getName());
    }

    /**
     * @param ResolverInterface $resolver
     * @return ContentTypeExtension
     */
    protected function getInstance(ResolverInterface $resolver)
    {
        return new ContentTypeExtension($resolver);
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
     * @param string $type
     * @return Content|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContent(string $type)
    {
        $mock = $this->createMock(Content::class);
        $mock->expects($this->atLeastOnce())
            ->method('getContentType')
            ->willReturn($type);

        return $mock;
    }

    /**
     * @param string $name
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContentType(string $name)
    {
        $mock = $this->createMock(ContentTypeInterface::class);
        $mock->expects($this->once())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }

    /**
     * @param string|null $type
     * @param ContentTypeInterface|null $contentType
     * @return ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver(string $type = null, ContentTypeInterface $contentType = null)
    {
        $mock = $this->createMock(ResolverInterface::class);
        if (null !== $type) {
            $mock->expects($this->any())
                ->method('getType')
                ->with($type)
                ->willReturn($contentType);
        }

        return $mock;
    }
}
