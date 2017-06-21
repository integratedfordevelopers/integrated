<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Connector;

use Integrated\Bundle\SocialBundle\Connector\Facebook\Manifest;

use Integrated\Common\Channel\Connector\AdapterInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Connector\ConfigurableInterface;
use Integrated\Common\Channel\Exporter\ExportableInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FacebookAdapter implements AdapterInterface, ConfigurableInterface, ExportableInterface
{
    const CONNECTOR_NAME = 'facebook';

    /**
     * @var Manifest
     */
    private $manifest;

    /**
     * @var ExportableInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param ExportableInterface $factory
     */
    public function __construct(ExportableInterface $factory)
    {
        $this->manifest = new Manifest();
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        return $this->manifest;
    }

    /**
     * {@inheritDoc}
     */
    public function getExporter(OptionsInterface $options)
    {
        return $this->factory->getExporter($options);
    }
}
