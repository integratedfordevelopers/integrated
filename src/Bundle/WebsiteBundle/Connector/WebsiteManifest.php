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

use Integrated\Common\Channel\Connector\Adapter\ManifestInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class WebsiteManifest implements ManifestInterface
{
    public const NAME = 'website';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'website';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Configure website';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.0';
    }
}
