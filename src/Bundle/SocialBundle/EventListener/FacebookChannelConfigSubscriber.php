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

use Facebook\Facebook;
use Integrated\Bundle\ChannelBundle\Event\FormConfigEvent;
use Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent;
use Integrated\Bundle\ChannelBundle\IntegratedChannelEvents;
use Integrated\Bundle\SocialBundle\Connector\FacebookAdapter;
use Integrated\Common\Channel\Connector\Config\ConfigManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This event listener will request a facebook access token for the facebook connector.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FacebookChannelConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var ConfigManagerInterface
     */
    private $manager;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param Facebook               $facebook
     * @param ConfigManagerInterface $manager
     * @param UrlGeneratorInterface  $generator
     */
    public function __construct(Facebook $facebook, ConfigManagerInterface $manager, UrlGeneratorInterface $generator)
    {
        $this->facebook = $facebook;
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

        if ($config->getAdapter() != FacebookAdapter::CONNECTOR_NAME) {
            return;
        }

        $options = $config->getOptions();

        if ($options->has('token')) {
            return;
        }

        $session = new Session();
        $session->set('externalReturnId', $config->getId());

        $event->setResponse(new RedirectResponse(
            $this->facebook->getRedirectLoginHelper()->getLoginUrl(
                $this->generator->generate(
                    'integrated_channel_config_external_return',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                ['publish_pages', 'manage_pages']
            )
        ));
    }

    /**
     * Check if the request got a access token and if to store it in the config.
     *
     * @param GetResponseConfigEvent $event
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function onRequest(GetResponseConfigEvent $event)
    {
        $config = $event->getConfig();

        if ($config->getAdapter() != FacebookAdapter::CONNECTOR_NAME) {
            return;
        }

        $token = $this->facebook->getRedirectLoginHelper()->getAccessToken();

        if (!$token) {
            return;
        }

        if (!$token->isLongLived()) {
            $token = $this->facebook->getOAuth2Client()->getLongLivedAccessToken($token);
        }

        $options = clone $config->getOptions();
        $options->set('token', (string) $token);

        $this->manager->persist($config->setOptions($options));

        $event->setResponse(new RedirectResponse(
            $this->generator->generate(
                'integrated_channel_config_edit',
                ['id' => $config->getId()]
            )
        ));
    }
}
