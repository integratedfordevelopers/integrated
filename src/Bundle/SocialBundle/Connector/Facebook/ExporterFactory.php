<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Connector\Facebook;

use Facebook\Facebook;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Exporter\ExportableInterface;
use RuntimeException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ExporterFactory implements ExportableInterface
{
    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @param Facebook $facebook
     */
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritdoc}
     */
    public function getExporter(OptionsInterface $options)
    {
        if (!$options->has('token')) {
            throw new RuntimeException('A access token is required to create a facebook exporter');
        }

        return new Exporter($this->facebook, $options->get('token'));
    }
}
