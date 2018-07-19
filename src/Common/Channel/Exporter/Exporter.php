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

use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Integrated\Bundle\ChannelBundle\Document\Embedded\Connector;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Content\ConnectorInterface;
use Integrated\Common\Content\ContentInterface;

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
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var ExporterInterface[][]
     */
    private $cache = [];

    /**
     * @param RegistryInterface $registry
     * @param ResolverInterface $resolver
     * @param DocumentManager   $dm
     */
    public function __construct(RegistryInterface $registry, ResolverInterface $resolver, DocumentManager $dm)
    {
        $this->registry = $registry;
        $this->resolver = $resolver;
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function export($content, $state, ChannelInterface $channel)
    {
        foreach ($this->getExporters($channel) as $exporter) {
            try {
                $response = $exporter->export($content, $state, $channel);

                if ($response instanceof ExporterReponse) {
                    $this->save($content, $response);
                }
            } catch (Exception $e) {
                // @todo probably should log this somewhere
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

            foreach ($this->resolver->getConfigs($channel) as $config) {
                try {
                    $adaptor = $this->registry->getAdapter($config->getAdapter());

                    if ($adaptor instanceof ExportableInterface) {
                        $exporters[] = $adaptor->getExporter($config);
                    }
                } catch (Exception $e) {
                    // @todo probably should log this somewhere
                }
            }

            $this->cache[$channel->getId()] = $exporters;
        }

        return $this->cache[$channel->getId()];
    }

    /**
     * @param ContentInterface $content
     * @param ExporterReponse  $exporterReponse
     */
    protected function save($content, ExporterReponse $exporterReponse)
    {
        if (!$content instanceof ContentInterface) {
            return;
        }

        if (!$content instanceof ConnectorInterface) {
            return;
        }

        $connector = new Connector();
        $connector
            ->setConfigId($exporterReponse->getConfigId())
            ->setConfigAdapter($exporterReponse->getConfigAdapter())
            ->setExternalId($exporterReponse->getExternalId())
        ;

        $content->addConnector($connector);

        $this->dm->flush($content);
    }
}
