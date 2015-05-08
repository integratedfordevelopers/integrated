<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Exporter;

use Exception;

use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\ChannelInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Exporter implements ExporterInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ExporterInterface[][]
     */
    private $cache = [];

    /**
     * @param RegistryInterface $registry
     * @param ResolverInterface $resolver
     */
    public function __construct(RegistryInterface $registry, ResolverInterface $resolver)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function export($content, $state, ChannelInterface $channel)
    {
        foreach ($this->getExporters($channel) as $exporter) {
            try {
                $exporter->export($content, $state, $channel);
            } catch (Exception $e) {
                // probably should log this somewhere
            }
        }
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return ExporterInterface[]
     */
    protected function getExporters(ChannelInterface $channel)
    {
        if (!array_key_exists($channel->getId(), $this->cache)) {
            $exporters = [];

            foreach($this->resolver->getConfigs($channel) as $config) {
                try {
                    $adaptor = $this->registry->getAdapter($config->getAdapter());

                    if ($adaptor instanceof ExportableInterface) {
                        $exporters[] = $adaptor->getExporter($config->getOptions());
                    }
                } catch (Exception $e) {
                    // probably should log this somewhere
                }
            }

            $this->cache[$channel->getId()] = $exporters;
        }

        return $this->cache[$channel->getId()];
    }
}
