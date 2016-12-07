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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Solr\Query\SuggestionQuery;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Select\Query\Component\FacetSet;
use Solarium\QueryType\Select\Result\Result;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

use Integrated\Bundle\ContentBundle\Solr\Serializer\SuggestionNormalizer;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class SuggestionNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set up
     */
    public function setUp()
    {
        $this->generator = $this->getMock(UrlGeneratorInterface::class);
        $this->resolver = $this->getMock(ResolverInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->getSuggestionNormalizer());
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * Testing if the first parameter is an instanceof Result
     */
    public function testNormalizerWithInvalidObject()
    {
        $this->getSuggestionNormalizer()->normalize(
            'test'
        );
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * Testing if the Result query function is an instanceof SuggestionQuery
     */
    public function testNormalizerWithInvalidQuery()
    {
        $object = $this->getSolrResult();

        $object
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn(null)
        ;

        $this->getSuggestionNormalizer()->normalize(
            $object
        );
    }

    public function testNormalizerWithEmptyData()
    {
        $object = $this->getSolrResult();

        $query = $this->getMock(SuggestionQuery::class);

        $facetSet = $this->getMock(FacetSet::class);

        $facetSet
            ->expects($this->once())
            ->method('getFacet')
            ->with('suggest')
            ->willReturn([])
        ;

        $object
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($facetSet)
        ;

        $object
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn([])
        ;

        $object
            ->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query)
        ;

        $this->assertEquals([], $this->getSuggestionNormalizer()->normalize(
            $object
        ));
    }

    public function testNormalizer()
    {
        $object = $this->getSolrResult();

        $query = $this->getMock(SuggestionQuery::class);

        $facetSet = $this->getMock(FacetSet::class);

        $facetSet
            ->expects($this->once())
            ->method('getFacet')
            ->with('suggest')
            ->willReturn([
                'suggestion' => 5,
                'suggest' => 2,
                'suggestions' => 4
            ])
        ;

        $object
            ->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query)
        ;

        $object
            ->expects($this->once())
            ->method('getFacetSet')
            ->willReturn($facetSet)
        ;

        $object
            ->expects($this->once())
            ->method('getDocuments')
            ->willReturn($this->getDocuments())
        ;

        $documents = $this->getDocuments();

        $this->assertEquals($documents[0]['type_id'], 'type');
        $this->assertEquals($documents[1]['title'], 'title document 2');

        $this->assertEquals(
            [
                'suggestions' => [
                    'suggestion',
                    'suggest',
                    'suggestions'
                ],
                'results' => [
                    0 => [
                        'id' => 'type',
                        'title' => 'title document',
                    ],
                    1 => [
                        'id' => 'type2',
                        'title' => 'title document 2'
                    ]
                ]
            ],
            $this->getSuggestionNormalizer()->normalize(
                $object
            )
        );
    }

    /**
     * @return SuggestionNormalizer
     */
    protected function getSuggestionNormalizer()
    {
        return new SuggestionNormalizer($this->generator, 'test-route', $this->resolver);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSolrResult()
    {
        return $this->getMock(
            Result::class,
            [],
            [
                Client::class,
                AbstractQuery::class,
                $this->getMock(
                    Response::class,
                    [],
                    [
                        '',
                        ''
                    ]
                )
            ]
        );
    }

    /**
     * @return array
     */
    protected function getDocuments()
    {
        return [
            [
                'title' => 'title document',
                'type_id' => 'type'
            ],
            [
                'title' => 'title document 2',
                'type_id' => 'type2'
            ]
        ];
    }
}
