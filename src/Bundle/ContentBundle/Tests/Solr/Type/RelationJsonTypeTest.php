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

use Integrated\Bundle\ContentBundle\Solr\Type\RelationJsonType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Converter\Container;
use Integrated\Common\Converter\ContainerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class RelationJsonTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\TypeInterface', $this->getInstance());
    }

    /**
     * @dataProvider buildProvider
     *
     * @param Content $content
     * @param array   $options
     * @param string  $expected
     */
    public function testBuild(Content $content, array $options, $expected)
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
            [
                $this->getStub(),
                [
                    'relation_id' => 'dummy',
                    'properties' => ['key' => 'id', 'type' => 'contentType'],
                    'alias' => 'field',
                ],
                [
                    'field' => [
                        json_encode([
                            ['key' => 'value', 'type' => 'content'],
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return RelationJsonType
     */
    protected function getInstance()
    {
        return new RelationJsonType();
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
        $relationStub = $this->createMock(Content::class);
        $relationStub->method('getId')->willReturn('value');
        $relationStub->method('getContentType')->willReturn('content');

        $stub = $this->createMock(Content::class);
        $stub->method('getReferencesByRelationId')->willReturn([$relationStub]);

        return $stub;
    }
}
