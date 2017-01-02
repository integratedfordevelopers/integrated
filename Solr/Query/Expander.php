<?php

namespace Integrated\Bundle\SolrBundle\Solr\Query;

use Integrated\Common\Solr\Query\Expander\ExpansionInterface;
use Integrated\Common\Solr\Query\ExpanderInterface;

use Solarium\Core\Query\AbstractQuery;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Expander implements ExpanderInterface
{
    /**
     * @var ExpansionInterface[]
     */
    protected $expansions = [];

    /**
     * {@inheritdoc}
     */
    public function addExpansion(ExpansionInterface $expansion)
    {
        $this->expansions[] = $expansion;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expand(AbstractQuery $query)
    {
        foreach ($this->expansions as $expansion) {
            if (!$expansion->supportsClass($query)) {
                continue;
            }

            $expansion->expand($query);
        }

        return $query;
    }
}