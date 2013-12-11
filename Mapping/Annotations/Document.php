<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Mapping\Annotations;

/**
 * Annotation for defining Solr options for a Document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @Annotation
 */
class Document
{
    /**
     * @var bool
     */
    protected $index = false;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    /**
     * @param bool $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIndex()
    {
        return $this->index;
    }
}