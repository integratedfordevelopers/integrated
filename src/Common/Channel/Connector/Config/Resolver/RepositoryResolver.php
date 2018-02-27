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

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ConfigRepositoryInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\Connector\Config\Util\ConfigIterator;
use Integrated\Common\Channel\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RepositoryResolver implements ResolverInterface
{
    /**
     * @var ConfigRepositoryInterface
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param ConfigRepositoryInterface $repository
     */
    public function __construct(ConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig($name)
    {
        return ($this->repository->find($name)) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if ($config = $this->repository->find($name)) {
            return $config;
        }

        throw new InvalidArgumentException(sprintf('Could not load config with the name "%s"', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs(ChannelInterface $channel)
    {
        return new ConfigIterator($this->repository->findByChannel($channel));
    }
}
