<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Query;

use Integrated\Bundle\ContentBundle\Solr\Normalizer;
use Integrated\Bundle\WorkflowBundle\EventListener\WorkflowMarkerInterface;
use Solarium\Component\Facet\Field;
use Solarium\Component\QueryInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SuggestionQuery extends Query implements WorkflowMarkerInterface
{
    /**
     * @var string
     */
    private $query = null;

    /**
     * @param string|array $options
     */
    public function __construct($options = null)
    {
        $this->setOption('query', '');
        $this->setOption('fields', 'id type_name type_class type_id title pub_time pub_edited');
        $this->setOption('rows', 5);
        $this->setOption('start', 0);

        $this->addTag('suggest');

        parent::__construct(is_scalar($options) ? ['query' => $options] : $options);
    }

    /**
     * Normalize the query string.
     *
     * @param string $query
     *
     * @return string
     */
    protected function normalize($query)
    {
        return Normalizer::normalize($query);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery(string $query, array $bind = null): QueryInterface
    {
        $this->query = $this->normalize($query);

        if (!$this->query) {
            throw new InvalidArgumentException('A query is required and can not be a empty string.');
        }

        $helper = $this->getHelper();

        $facet = [
            'field' => 'suggestions',
            'limit' => 5,
            'prefix' => $helper->escapeTerm($this->query),
            'method' => 'enum',
        ];

        $facet = new Field($facet);
        $facet->setKey('suggest');
        $facet->addExclude('suggest');

        $this->getFacetSet()
            ->removeFacet('suggest')
            ->addFacet($facet);

        return parent::setQuery(sprintf(
            'title:((%1$s)^50 OR (%1$s~2)^20 OR(%2$s)^10 OR (%2$s*) OR (%2$s~))',
            $helper->escapePhrase($this->query),
            '+'.str_replace(' ', ' +', $helper->escapeTerm($this->query))
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery($original = false): ?string
    {
        if ($original) {
            return $this->query;
        }

        return parent::getQuery();
    }
}
