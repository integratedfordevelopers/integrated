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
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface as ModelConfigInterface;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;
use Integrated\Common\Channel\Exception\UnexpectedTypeException;
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
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @param Facebook $facebook
     * @param UrlResolver $urlResolver
     */
    public function __construct(Facebook $facebook, UrlResolver $urlResolver)
    {
        $this->facebook = $facebook;
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getExporter(ConfigInterface $config)
    {
        if (!$config instanceof ModelConfigInterface) {
            throw new UnexpectedTypeException($config, ModelConfigInterface::class);
        }

        $options = $config->getOptions();

        if (!$options instanceof OptionsInterface) {
            throw new UnexpectedTypeException($options, OptionsInterface::class);
        }

        // It should contain at least the token.
        if (!$options->has('token')) {
            throw new RuntimeException('A access token is required to create a facebook exporter');
        }

        return new Exporter($this->facebook, $config, $this->urlResolver);
    }
}
