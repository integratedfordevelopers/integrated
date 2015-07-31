<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Connector;

use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Connector\ConfigurableInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WebsiteAdapter implements AdapterInterface, ConfigurableInterface
{
    /**
     * @var WebsiteManifest
     */
    private $manifest;

    /**
     * @var WebsiteConfiguration
     */
    private $configuration;

    /**
     * {@inheritdoc}
     */
    public function getManifest()
    {
        if (null === $this->manifest) {
            $this->manifest = new WebsiteManifest();
        }

        return $this->manifest;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        if (null === $this->configuration) {
            $this->configuration = new WebsiteConfiguration();
        }

        return $this->configuration;
    }
}
