<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Form\Type;

use Facebook\Facebook;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FacebookType extends AbstractType
{
    /**
     * @var Facebook $facebook
     */
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $facebook = $this->facebook;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($facebook) {
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

                    if (count($pages) == 0) {
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
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($facebook) {
            $formData = $event->getData();
            if ($formData && $formData['token'] && $formData['page'] && is_numeric($formData['page'])) {
                $pageToken = null;
                if ($formData['page_token']) {
                    $response = $this->facebook->get(
                        '/debug_token?input_token=' . $formData['page_token'],
                        $formData['page_token'],
                        null,
                        'v3.2'
                    );

                    $data = $response->getDecodedBody();
                    if ($data['data']["profile_id"] == $formData['page']) {
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
        });

        $builder->add('token', TextType::class, ['attr' => ['readonly' => 'true']]);
        $builder->add('apiStatus', TextType::class, ['attr' => ['readonly' => 'true']]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_social_facebook';
    }
}
