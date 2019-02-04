<?php

namespace Integrated\Bundle\SocialBundle\Form\EventListener;

use Facebook\Facebook;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFacebookPageFieldListener implements EventSubscriberInterface
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
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formData = $event->getData();

        if ($formData && $formData['token']) {
            try {
                $response = $this->facebook->get(
                    '/me/accounts?type=page',
                    $formData['token'],
                    null,
                    'v3.2'
                );

                $data = $response->getDecodedBody();
                $pages = [];

                if ($data['data']) {
                    foreach ($data['data'] as $page) {
                        $pages[$page['name']] = $page['id'];
                    }
                }

                ksort($pages);

                $form->add('page', ChoiceType::class, ['choices' => $pages, 'label' => 'Facebook page']);

                if (\count($pages) == 0) {
                    $formData['apiStatus'] = 'Your account does not seem to be administrator of a Facebook page';
                } else {
                    $formData['apiStatus'] = 'OK';

                    $form->add('page_token', TextType::class, ['attr' => ['readonly' => 'true']]);
                }
            } catch (\Exception $e) {
                $formData['token'] = null;
                $formData['apiStatus'] = 'Token seems to be invalid. Save the form to get a new one. ('.$e->getMessage().')';
            }
        } else {
            $formData['apiStatus'] = 'Save the configuration to connect to Facebook.';
        }
    }
}
