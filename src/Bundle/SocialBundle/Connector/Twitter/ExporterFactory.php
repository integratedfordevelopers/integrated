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

use Integrated\Bundle\SocialBundle\Factory\TwitterFactory;
use Integrated\Bundle\ChannelBundle\Model\ConfigInterface as ModelConfigInterface;
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
     * @var TwitterFactory
     */
    private $factory;

    /**
     * @param TwitterFactory $factory
     */
    public function __construct(TwitterFactory $factory)
    {
        $this->factory = $factory;
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

        if (!$options->has('token') || !$options->has('token_secret')) {
            throw new RuntimeException('A access token and secret are required to create a twitter exporter');
        }

        return new Exporter(
            $this->factory->createClient($options->get('token'), $options->get('token_secret')),
            $config
        );
    }
}
