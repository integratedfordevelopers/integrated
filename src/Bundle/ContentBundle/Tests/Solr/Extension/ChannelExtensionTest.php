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

use Integrated\Bundle\ContentBundle\Solr\Extension\ChannelExtension;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;
use stdClass;

/**
 * @covers \Integrated\Bundle\ContentBundle\Solr\Extension\ChannelExtension
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeExtensionInterface', $this->getInstance($this->getResolver()));
    }

    /**
     * @dataProvider buildProvider
     */
    public function testBuild(ChannelableInterface $content, array $expected)
    {
        $container = $this->getContainer();

        $this->getInstance($this->getResolver())->build($container, $content);
        $this->getInstance($this->getResolver())->build($container, $content); // should clear previous build

        self::assertEquals($expected, $container->toArray());
    }

    public function buildProvider()
    {
        return [
            [
                $this->getContent([]),
                [],
            ],
            [
                $this->getContent([$this->getChannel('id1'), $this->getChannel('id2')]),
                ['facet_channels' => ['id1', 'id2']],
            ],
            [
                $this->getContent([$this->getChannel('id1'), new stdClass(), $this->getChannel('id2')]),
                ['facet_channels' => ['id1', 'id2']],
            ],
            [
                $this->getContent([new stdClass(), new stdClass()]),
                [],
            ],
        ];
    }

    public function testBuildNotChannelable()
    {
        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');
        $container->expects($this->never())
            ->method($this->anything());

        /* @var ContainerInterface $container */

        $this->getInstance($this->getResolver())->build($container, new stdClass());
    }

    public function testGetName()
    {
        self::assertEquals('integrated.content', $this->getInstance($this->getResolver())->getName());
    }

    /**
     * @param ResolverInterface $resolver
     *
     * @return ChannelExtension
     */
    protected function getInstance(ResolverInterface $resolver)
    {
        return new ChannelExtension($resolver);
    }

    /**
     * @param ChannelInterface[] $channels
     *
     * @return ChannelableInterface
     */
    protected function getContent(array $channels)
    {
        $mock = $this->createMock('Integrated\\Common\\Content\\ChannelableInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getChannels')
            ->willReturn($channels);

        return $mock;
    }

    /**
     * @param string $id
     *
     * @return ChannelInterface
     */
    private function getChannel($id)
    {
        $mock = $this->createMock('Integrated\\Common\\Content\\Channel\\ChannelInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        return $mock;
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
     * @param string|null               $type
     * @param ContentTypeInterface|null $contentType
     *
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
