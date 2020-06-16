<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Connector\Config\Resolver;

use AppendIterator;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\Connector\Config\Util\UniqueConfigIterator;
use Integrated\Common\Channel\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolver implements ResolverInterface
{
    /**
     * @var ResolverInterface[]
     */
    protected $resolvers;

    /**
     * @param ResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @param string $name
     *
     * @return ResolverInterface|null
     */
    protected function findResolver($name)
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->hasConfig($name)) {
                return $resolver;
            }
        }

        return null;
    }

    /**
     * @return ResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig($name)
    {
        if ($this->findResolver($name)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if ($resolver = $this->findResolver($name)) {
            return $resolver->getConfig($name);
        }

        throw new InvalidArgumentException(sprintf('Could not load config with the name "%s"', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs(ChannelInterface $channel)
    {
        $iterator = new AppendIterator();

        foreach ($this->resolvers as $resolver) {
            $iterator->append($resolver->getConfigs($channel));
        }

        return new UniqueConfigIterator($iterator);
    }
}
