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
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Adapter\RegistryInterface;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Channel\Connector\ExporterInterface as ConnectorExporterInterface;
use Integrated\Common\Content\ConnectorInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\PublishableInterface;

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
        $publicationDate = null;
        if ($content instanceof PublishableInterface) {
            $publicationDate = $content->getPublishTime()->getStartDate();
            if (!$content->isPublished()) {
                //make sure content is not published when publication date or state
                //has changed after queueing
                $state = ConnectorExporterInterface::STATE_DELETE;
            }
        }

        foreach ($this->getExporters($channel, $publicationDate) as $exporter) {
            try {
                $response = $exporter->export($content, $state, $channel);

                if ($response instanceof ExporterResponse) {
                    $this->save($content, $response);
                }
            } catch (Exception $e) {
                // @todo probably should log this somewhere
            }
        }
    }

    /**
     * @param ChannelInterface $channel
     * @param ?DateTime        $publicationDate
     *
     * @return ExporterInterface[]
     */
    protected function getExporters(ChannelInterface $channel, $publicationDate)
    {
        if (!\array_key_exists($channel->getId(), $this->cache)) {
            $exporters = [];

            foreach ($this->resolver->getConfigs($channel) as $config) {
                try {
                    $publicationStartDate = $config->getPublicationStartDate();
                    if ($publicationStartDate && $publicationDate && $publicationStartDate > $publicationDate) {
                        continue;
                    }

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
     * @param object           $content
     * @param ExporterResponse $response
     */
    protected function save($content, ExporterResponse $response)
    {
        if (!$content instanceof ContentInterface) {
            return;
        }

        if (!$content instanceof ConnectorInterface) {
            return;
        }

        if ($content->hasConnector($response->getConfigId())) {
            $content->getConnector($response->getConfigId())
                ->setConfigAdapter($response->getConfigAdapter())
                ->setExternalId($response->getExternalId());
        } else {
            $content->addConnector((new Connector())
                ->setConfigId($response->getConfigId())
                ->setConfigAdapter($response->getConfigAdapter())
                ->setExternalId($response->getExternalId()));
        }

        $this->dm->flush($content);
    }
}
