<?php

namespace Integrated\Bundle\SocialBundle\Form\EventListener;

use Facebook\Facebook;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SetFacebookPageTokenListener implements EventSubscriberInterface
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
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function onSubmit(FormEvent $event)
    {
        $formData = $event->getData();
        if ($formData && $formData['token'] && $formData['page'] && is_numeric($formData['page'])) {
            $pageToken = null;
            if ($formData['page_token']) {
                $response = $this->facebook->get(
                    '/debug_token?input_token='.$formData['page_token'],
                    $formData['page_token'],
                    null,
                    'v3.2'
                );

                $data = $response->getDecodedBody();
                if ($data['data']['profile_id'] == $formData['page']) {
                    $pageToken = $formData['page_token'];
                }
            }

            if (!$pageToken) {
                $response = $this->facebook->get(
                    '/'.$formData['page'].'?fields=access_token',
                    $formData['token'],
                    null,
                    'v3.2'
                );

                $tokenData = $response->getDecodedBody();

                $data = $event->getData();
                $data['page_token'] = $tokenData['access_token'];
                $event->setData($data);
            }
        }
    }
}
