<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Integrated\Bundle\FormTypeBundle\Form\Type\ColorType;
use Integrated\Bundle\StorageBundle\Form\Type\ImageDropzoneType;
use Integrated\Bundle\UserBundle\Model\Scope;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);

        $builder->add('logo', ImageDropzoneType::class);
        $builder->add('color', ColorType::class);

        $builder->add('domains', BootstrapCollectionType::class, [
            'label' => 'Domains (example.com)',
            'allow_add' => true,
            'allow_delete' => true,
            'add_button_text' => 'Add domain',
            'delete_button_text' => 'Delete domain',
            'sub_widget_col' => 5,
            'button_col' => 3,
            'attr' => ['class' => 'channel-domains'],
        ]);

        $builder->add('primaryDomain', HiddenType::class, ['attr' => ['class' => 'primary-domain-input']]);

        $builder->add('primaryDomainRedirect', CheckboxType::class, ['label' => 'Redirect to primary domain']);

        //validate domain names
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (empty($data['domains'])) {
                return;
            }

            $primaryChannelIsIteratedAndEmpty = false;

            foreach ($data['domains'] as $domain) {
                $domain = trim($domain);
                $primary = trim($data['primaryDomain']);

                if ($domain == '' && ($primaryChannelIsIteratedAndEmpty || $domain != $primary)) {
                    $form->get('domains')->addError(new FormError('Domain name can not be empty (only primary)'));
                } elseif (preg_match('/[\s\\\[\],;:+\/\?^`=&%"\'#<>@*!()|]/', $domain, $matches)) {
                    $form->get('domains')->addError(
                        new FormError(
                            sprintf('Character "%s" in domain name "%s" is not allowed', $matches[0], $domain)
                        )
                    );
                }

                if ($primary == '' && $domain == $primary) {
                    $primaryChannelIsIteratedAndEmpty = true;
                }
            }
        });

        $builder->add(
            'scope',
            EntityType::class,
            [
                'required' => false,
                'class' => Scope::class,
                'placeholder' => 'No user login allowed',
                'label' => 'User scope',
                'choice_label' => 'name',
            ]
        );

        $builder->add('permissions', PermissionsType::class, [
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'channel';
    }
}
