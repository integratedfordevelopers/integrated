<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\EventListener;

use Integrated\Bundle\ChannelBundle\Event\FormConfigEvent;
use Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent;
use Integrated\Bundle\ChannelBundle\IntegratedChannelEvents;
use Integrated\Bundle\SocialBundle\Connector\TwitterAdapter;
use Integrated\Bundle\SocialBundle\Factory\TwitterFactory;
use Integrated\Common\Channel\Connector\Config\ConfigManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This event listener will request a twitter access token for the twitter connector.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TwitterChannelConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @var TwitterFactory
     */
    private $factory;

    /**
     * @var ConfigManagerInterface
     */
    private $manager;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param TwitterFactory         $factory
     * @param ConfigManagerInterface $manager
     * @param UrlGeneratorInterface  $generator
     */
    public function __construct(TwitterFactory $factory, ConfigManagerInterface $manager, UrlGeneratorInterface $generator)
    {
        $this->factory = $factory;
        $this->manager = $manager;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            IntegratedChannelEvents::CONFIG_CREATE_SUBMITTED => 'onSubmit',
            IntegratedChannelEvents::CONFIG_EDIT_REQUEST => 'onRequest',
            IntegratedChannelEvents::CONFIG_EDIT_SUBMITTED => 'onSubmit',
        ];
    }

    /**
     * Request a access token the current config is missing one.
     *
     * @param FormConfigEvent $event
     */
    public function onSubmit(FormConfigEvent $event)
    {
        $config = $event->getConfig();

        if ($config->getAdapter() != TwitterAdapter::CONNECTOR_NAME) {
            return;
        }

        $options = $config->getOptions();

        if ($options->has('token') && $options->has('token_secret')) {
            return;
        }

        $client = $this->factory->createClient();

        $session = new Session();
        $session->set('externalReturnId', $config->getId());

        $response = $client->oauth(
            'oauth/request_token',
            [
                'oauth_callback' => $this->generator->generate(
                    'integrated_channel_config_external_return',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]
        );

        // Store the request token as we need it later on in the twitter callback {@see onRequest}
        $options = clone $config->getOptions();

        $options->set('request_token', $response['oauth_token']);
        $options->set('request_token_secret', $response['oauth_token_secret']);

        $config->setOptions($options);

        $event->setResponse(new RedirectResponse(
            $client->url('oauth/authorize', ['oauth_token' => $response['oauth_token']])
        ));
    }

    /**
     * Check if the request got a oauth verifier and if so use that to get a access token
     * and store it in the config.
     *
     * @param GetResponseConfigEvent $event
     */
    public function onRequest(GetResponseConfigEvent $event)
    {
        $config = $event->getConfig();

        if ($config->getAdapter() != TwitterAdapter::CONNECTOR_NAME) {
            return;
        }

        $options = $config->getOptions();

        if (!$options->has('request_token') || !$options->has('request_token_secret')) {
            return;
        }

        $verifier = $event->getRequest()->get('oauth_verifier');

        if (!$verifier) {
            return;
        }

        $response = $this->factory->createClient($options->get('request_token'), $options->get('request_token_secret'))
            ->oauth('oauth/access_token', ['oauth_verifier' => $verifier]);

        $options = clone $options;
        $options
            ->set('token', $response['oauth_token'])
            ->set('token_secret', $response['oauth_token_secret'])

            ->remove('request_token')
            ->remove('request_token_secret');

        $this->manager->persist($config->setOptions($options));

        $event->setResponse(new RedirectResponse(
            $this->generator->generate(
                'integrated_channel_config_edit',
                ['id' => $config->getId()]
            )
        ));
    }
}
