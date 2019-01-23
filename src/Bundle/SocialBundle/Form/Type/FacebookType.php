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

                    $formData['apiStatus'] = 'OK';

                    if (isset($formData['page']) && is_numeric($formData['page'])) {
                        $response = $this->facebook->get(
                            '/'.$formData['page'].'?fields=access_token',
                            $formData['token'],
                            null,
                            'v3.2'
                        );

                        $data = $response->getDecodedBody();
                        $form->add('page_token', TextType::class, ['attr' => ['readonly' => 'true'], 'data' => $data['access_token']]);

                    }
                } catch (\Exception $e) {
                    $formData['token'] = null;
                    $formData['apiStatus'] = 'Token seems to be invalid. Save the form to get a new one.';
                }

            } else {
                $formData['apiStatus'] = 'Save the configuration to connect to Facebook.';
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
