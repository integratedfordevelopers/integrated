<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer;

use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Normalizer implements NormalizerInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, array $options = [])
    {
        if (\is_object($object)) {
            return $this->resolver->getProcessor($object)->process($object, new Context($this->resolver, $options));
        }

        return [];
    }
}
