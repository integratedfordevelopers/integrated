<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Serializer;

use Integrated\Bundle\ContentBundle\Solr\Query\SuggestionQuery;
use Integrated\Common\ContentType\ResolverInterface;
use Solarium\Core\Query\DocumentInterface;
use Solarium\QueryType\Select\Result\Result;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SuggestionNormalizer implements NormalizerInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var string
     */
    private $route;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $generator
     * @param string                $route
     * @param ResolverInterface     $resolver
     */
    public function __construct(UrlGeneratorInterface $generator, $route, ResolverInterface $resolver)
    {
        $this->generator = $generator;
        $this->route = $route;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @param Result $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object)) {
            throw new InvalidArgumentException(sprintf(
                'The object must be a instance of "%s" with a query instance of "%s".',
                Result::class,
                SuggestionQuery::class
            ));
        }

        $data = [
            'suggestions' => [],
            'results' => [],
        ];

        foreach ($object->getFacetSet()->getFacet('suggest') as $term => $count) {
            $data['suggestions'][] = $term;
        }

        foreach ($object->getDocuments() as $document) {
            $data['results'][] = [
                'id' => (string) $document['type_id'],
                'type' => $this->getType($document),
                'title' => (string) $document['title'],
                'url' => $this->getUrl($document),
                'published' => $this->getDate($document, 'pub_time'),
                'updated' => $this->getDate($document, 'pub_edited'),
            ];
        }

        return ['query' => $object->getQuery()->getQuery(true)] + array_filter($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Result && $data->getQuery() instanceof SuggestionQuery;
    }

    /**
     * @param DocumentInterface $document
     *
     * @return string
     */
    protected function getType(DocumentInterface $document)
    {
        if ($this->resolver->hasType($document['type_name'])) {
            return $this->resolver->getType($document['type_name'])->getName();
        }

        return (string) $document['type_name'];
    }

    /**
     * @param DocumentInterface $document
     *
     * @return string
     */
    protected function getUrl(DocumentInterface $document)
    {
        return $this->generator->generate($this->route, ['id' => $document['type_id']]);
    }

    /**
     * @param DocumentInterface $document
     * @param string            $field
     *
     * @return string
     */
    protected function getDate(DocumentInterface $document, $field)
    {
        if (!isset($document[$field]) || !strtotime($document[$field])) {
            return null;
        }

        return $document[$field];
    }
}
