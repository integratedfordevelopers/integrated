<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Connector\Twitter;

use Integrated\Bundle\SocialBundle\Connector\TwitterAdapter;
use Integrated\Bundle\SocialBundle\Form\Type\TwitterType;
use Integrated\Common\Channel\Connector\Adapter\ManifestInterface;
use Integrated\Common\Channel\Connector\ConfigurationInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Manifest implements ManifestInterface, ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return TwitterAdapter::CONNECTOR_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return 'Twitter';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Twitter social adapter';
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {
        return TwitterType::class;
    }
}
