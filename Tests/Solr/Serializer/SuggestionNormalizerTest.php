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

use Solarium\QueryType\Select\Query\Component\FacetSet;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class SuggestionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlGeneratorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $generator;

    /**
     * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolver;

    /**
     * @var Result | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $result;

    /**
     * @var string
     */
    protected $route = 'test-route';

    /**
     * Set up
     */
    public function setUp()
    {
        $this->generator = $this->getMock(UrlGeneratorInterface::class);
        $this->resolver = $this->getMock(ResolverInterface::class);
        $this->result = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Normalizer must be an instance of the NormalizerInterface
     */
    public function testInterface()
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->getInstance());
    }

    /**
     * Testing if the first parameter is an instanceof Result
     * @expectedException InvalidArgumentException
     */
    public function testNormalizerWithInvalidObject()
    {
        $this->getInstance()->normalize('test');
    }

    /**
     * Testing if the Result query function is an instanceof SuggestionQuery
     * @expectedException InvalidArgumentException
     */
    public function testNormalizerWithInvalidQuery()
    {
        $this->result
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn(null)
        ;

        $this->getInstance()->normalize($this->result);
    }

    /**
     * Test the Normalizer with empty data
     */
    public function testNormalizerWithEmptyData()
    {
        $query = $this->getMock(SuggestionQuery::class);
        $facetSet = $this->getMock(FacetSet::class);

        $facetSet
            ->expects($this->once())
            ->method('getFacet')
            ->with('suggest')
            ->willReturn([])
        ;

        $this->result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($facetSet)
        ;

        $this->result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn([])
        ;

        $this->result
            ->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query)
        ;

        $this->assertEquals([], $this->getInstance()->normalize($this->result));
    }

    /**
     * Test the Normalizer with data
     */
    public function testNormalizerWithData()
    {
        $query = $this->getMock(SuggestionQuery::class);
        $facetSet = $this->getMock(FacetSet::class);

        $suggestions = [
            'suggestion' => 5,
            'suggest' => 0,
            'suggestions' => 4
        ];

        $facetSet
            ->expects($this->once())
            ->method('getFacet')
            ->with('suggest')
            ->willReturn($suggestions)
        ;

        $this->result
            ->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query)
        ;

        $this->result
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($facetSet)
        ;

        $documents = [
            new Document(
                [
                    'type_id' => 'id_0',
                    'type_name' => 'news',
                    'title' => 'title_0',
                    'pub_time' => 'Invalid timestamp',
                    'pub_edited' => '2015-01-01 08:00'
                ]
            ),
            new Document(
                [
                    'type_id' => 'id_1',
                    'type_name' => 'convert_to_news',
                    'title' => 'title_1',
                    'pub_time' => '2015-01-01',
                ]
            )
        ];

        $this->resolver
            ->expects($this->exactly(2))
            ->method('hasType')
            ->withConsecutive(
                [$this->equalTo('news')],
                [$this->equalTo('convert_to_news')]
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            )
        ;

        $contentType = $this->getMock(ContentTypeInterface::class);

        $this->resolver
            ->expects($this->once())
            ->method('getType')
            ->with($this->equalTo('convert_to_news'))
            ->willReturn($contentType)
        ;

        $contentType
            ->expects($this->once())
            ->method('getName')
            ->willReturn('news')
        ;

        $this->generator
            ->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(
                [$this->route, ['id' => 'id_0']],
                [$this->route, ['id' => 'id_1']]
            )
            ->willReturnOnConsecutiveCalls(
                'url_0',
                'url_1'
            )
        ;

        $this->result
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn($documents)
        ;

        $this->assertEquals(
            [
                'suggestions' => array_keys($suggestions),
                'results' => [
                    [
                        'id' => 'id_0',
                        'type' => 'news',
                        'title' => 'title_0',
                        'url' => 'url_0',
                        'published' => null,
                        'updated' => '2015-01-01 08:00'
                    ],
                    [
                        'id' => 'id_1',
                        'type' => 'news',
                        'title' => 'title_1',
                        'url' => 'url_1',
                        'published' => '2015-01-01',
                        'updated' => null
                    ],
                ]
            ],
            $this->getInstance()->normalize($this->result)
        );
    }

    /**
     * @return SuggestionNormalizer
     */
    protected function getInstance()
    {
        return new SuggestionNormalizer($this->generator, $this->route, $this->resolver);
    }
}
