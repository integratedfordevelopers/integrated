<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Resolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class UriResolver implements ResolverInterface
{
    /**
     * @var string
     */
    protected $options;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * {@inheritdoc}
     */
    public function __construct($options, $identifier)
    {
        $this->options = $options;
        $this->identifier = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return sprintf('%s/%s', $this->options['public'], $this->identifier);
    }
}
