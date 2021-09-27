<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Solr\Serializer;

use Integrated\Bundle\ContentBundle\Solr\Query\SuggestionQuery;
use Integrated\Bundle\ContentBundle\Solr\Serializer\SuggestionNormalizer;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ResolverInterface;
use Solarium\Component\Result\Facet\Field;
use Solarium\Component\Result\FacetSet;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;
use Solarium\QueryType\Suggester\Query;
use stdClass;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class SuggestionNormalizerTest extends \PHPUnit\Framework\TestCase
{
    const ROUTE = 'this-is-the-route';

    /**
     * @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $generator;

    /**
     * @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    protected function setUp(): void
    {
        $this->generator = $this->createMock(UrlGeneratorInterface::class);
        $this->resolver = $this->createMock(ResolverInterface::class);
    }

    protected function setUpNormalize()
    {
        $this->resolver->expects($this->atLeastOnce())
            ->method('hasType')
            ->willReturnMap([
                ['news', true],
                ['blog', true],
                ['invalid', false],
                [null, false],
            ]);

        $this->resolver->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturnMap([
                ['news', $this->getContentType('this-is-news')],
                ['blog', $this->getContentType('this-is-a-blog')],
            ]);

        $this->generator->expects($this->atLeastOnce())
            ->method('generate')
            ->willReturnCallback(function ($route, $params) {
                self::assertEquals(self::ROUTE, $route);
                self::assertArrayHasKey('id', $params);
                self::assertCount(1, $params);

                $map = [
                    'id_0' => 'url_0',
                    'id_1' => 'url_1',
                    'id_2' => 'url_2',
                    'id_3' => 'url_3',
                ];

                if (\array_key_exists($params['id'], $map)) {
                    return $map[$params['id']];
                }

                return '';
            });
    }

    protected function setUpNormalizeNoCalls()
    {
        $this->resolver->expects($this->never())
            ->method($this->anything());

        $this->generator->expects($this->never())
            ->method($this->anything());
    }

    public function testInterface()
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->getInstance());
    }

    public function testNormalizeWithInvalidObject()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->getInstance()->normalize('invalid');
    }

    public function testNormalizeWithOutQuery()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->getInstance()->normalize($this->getQueryResult(new Query()));
    }

    public function testNormalizeWithInvalidQuery()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->getInstance()->normalize($this->getQueryResult(new Query()));
    }

    public function testNormalize()
    {
        $result = $this->getQueryResult($this->getQuery('this-is-the-query'));

        $suggestions = [
            'suggestion' => 5,
            'suggest' => 0,
            'suggestions' => 4,
        ];

        $result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($this->getFacetSet(new Field($suggestions)));

        $result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn($this->getDocuments());

        $this->setUpNormalize();

        $expected = [
            'query' => 'this-is-the-query',
            'suggestions' => array_keys($suggestions),
            'results' => $this->getDocumentsExpected(),
        ];

        self::assertSame($expected, $this->getInstance()->normalize($result));
    }

    public function testNormalizeWithNoResults()
    {
        $result = $this->getQueryResult($this->getQuery('this-is-the-query'));

        $result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($this->getFacetSet(new Field([])));

        $result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn([]);

        $this->setUpNormalizeNoCalls();

        self::assertSame(['query' => 'this-is-the-query'], $this->getInstance()->normalize($result));
    }

    public function testNormalizeWithNoSuggestions()
    {
        $result = $this->getQueryResult($this->getQuery('this-is-the-query'));

        $result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($this->getFacetSet(new Field([])));

        $result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn($this->getDocuments());

        $this->setUpNormalize();

        $expected = [
            'query' => 'this-is-the-query',
            'results' => $this->getDocumentsExpected(),
        ];

        self::assertSame($expected, $this->getInstance()->normalize($result));
    }

    public function testNormalizeWithNoDocuments()
    {
        $result = $this->getQueryResult($this->getQuery('this-is-the-query'));

        $suggestions = [
            'suggestion' => 5,
            'suggest' => 0,
            'suggestions' => 4,
        ];

        $result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($this->getFacetSet(new Field($suggestions)));

        $result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn([]);

        $this->setUpNormalizeNoCalls();

        $expected = [
            'query' => 'this-is-the-query',
            'suggestions' => array_keys($suggestions),
        ];

        self::assertSame($expected, $this->getInstance()->normalize($result));
    }

    public function testNormalizeWithNoResultsAndEmptyQuery()
    {
        $result = $this->getQueryResult($this->getQuery(''));

        $result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($this->getFacetSet(new Field([])));

        $result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn([]);

        self::assertSame(['query' => ''], $this->getInstance()->normalize($result));
    }

    public function testSupportsNormalization()
    {
        $normalizer = $this->getInstance();

        self::assertFalse($normalizer->supportsNormalization(null));
        self::assertFalse($normalizer->supportsNormalization('invalid'));
        self::assertFalse($normalizer->supportsNormalization(new stdClass()));
        self::assertFalse($normalizer->supportsNormalization($this->getQueryResult(new Query())));
        self::assertTrue($normalizer->supportsNormalization($this->getQueryResult($this->getQuery())));
    }

    /**
     * @return SuggestionNormalizer
     */
    protected function getInstance()
    {
        return new SuggestionNormalizer($this->generator, self::ROUTE, $this->resolver);
    }

    /**
     * @param object|null $query
     *
     * @return Result|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQueryResult($query = null)
    {
        $mock = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
        $mock
            ->expects($this->atLeastOnce())
            ->method('getQuery')
            ->willReturn($query);

        return $mock;
    }

    /**
     * @param string|null $query
     *
     * @return SuggestionQuery|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQuery($query = null)
    {
        $mock = $this->getMockBuilder(SuggestionQuery::class)->disableOriginalConstructor()->getMock();
        $mock
            ->expects($this->any())
            ->method('getQuery')
            ->with($this->equalTo(true))
            ->willReturn($query);

        return $mock;
    }

    /**
     * @param array $facets
     *
     * @return FacetSet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFacetSet($facets = null)
    {
        $mock = $this->getMockBuilder(FacetSet::class)->disableOriginalConstructor()->getMock();
        $mock
            ->expects($this->once())
            ->method('getFacet')
            ->with('suggest')
            ->willReturn($facets);

        return $mock;
    }

    /**
     * @param string $name
     *
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContentType($name)
    {
        $mock = $this->createMock(ContentTypeInterface::class);
        $mock
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }

    /**
     * @return Document[]
     */
    protected function getDocuments()
    {
        return [
            new Document([
                'type_id' => 'id_0',
                'type_name' => 'news',
                'title' => 'title_0',
                'pub_time' => 'invalid',
                'pub_edited' => 'invalid',
            ]),
            new Document([
                'type_id' => 'id_1',
                'type_name' => 'blog',
                'pub_time' => '2012-12-12T12:12:12Z',
            ]),
            new Document([
                'type_id' => 'id_2',
                'type_name' => 'invalid',
                'title' => 'title_2',
                'pub_edited' => '2012-12-12T12:12:12Z',
            ]),
            new Document([
                'type_id' => 'id_3',
                'type_name' => 'blog',
                'title' => 'title_3',
                'pub_time' => '2012-12-12T12:12:12Z',
                'pub_edited' => 'invalid',
            ]),
            new Document([]),
        ];
    }

    /**
     * @return array
     */
    protected function getDocumentsExpected()
    {
        return [
            [
                'id' => 'id_0',
                'type' => 'this-is-news',
                'title' => 'title_0',
                'url' => 'url_0',
                'published' => null,
                'updated' => null,
            ],
            [
                'id' => 'id_1',
                'type' => 'this-is-a-blog',
                'title' => '',
                'url' => 'url_1',
                'published' => '2012-12-12T12:12:12Z',
                'updated' => null,
            ],
            [
                'id' => 'id_2',
                'type' => 'invalid',
                'title' => 'title_2',
                'url' => 'url_2',
                'published' => null,
                'updated' => '2012-12-12T12:12:12Z',
            ],
            [
                'id' => 'id_3',
                'type' => 'this-is-a-blog',
                'title' => 'title_3',
                'url' => 'url_3',
                'published' => '2012-12-12T12:12:12Z',
                'updated' => null,
            ],
            [
                'id' => '',
                'type' => '',
                'title' => '',
                'url' => '',
                'published' => null,
                'updated' => null,
            ],
        ];
    }
}
