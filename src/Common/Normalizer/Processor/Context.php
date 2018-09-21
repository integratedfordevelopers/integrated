<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Processor;

use Integrated\Common\Normalizer\NormalizerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Context implements NormalizerInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Context
     */
    private $nesting = null;

    /**
     * @param ResolverInterface $resolver
     * @param array             $options
     * @param Context           $nesting
     */
    public function __construct(ResolverInterface $resolver, array $options, self $nesting = null)
    {
        $this->resolver = $resolver;
        $this->options = $options;
        $this->nesting = $nesting;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Context
     */
    public function getNesting()
    {
        return $this->nesting;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, array $options = [])
    {
        if (\is_object($object)) {
            return $this->resolver->getProcessor($object)->process($object, new self($this->resolver, $options, $this));
        }

        return [];
    }
}
