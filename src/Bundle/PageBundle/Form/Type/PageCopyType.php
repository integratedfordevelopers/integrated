<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type;

use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotEqualTo;

class PageCopyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', HiddenType::class);

        $builder->add('sourceChannel', ChannelChoiceType::class, [
            'label' => 'Source channel',
            'placeholder' => '-- choose a source channel --',
            'attr' => [
                'onchange' => '$(\'#page_copy_action\').val(\'refresh\');document.page_copy.submit();',
            ],
        ]);

        $builder->add('targetChannel', ChannelChoiceType::class, [
            'label' => 'Target channel',
            'placeholder' => '-- choose a target channel --',
            'attr' => [
                'onchange' => '$(\'#page_copy_action\').val(\'refresh\');document.page_copy.submit();',
            ],
            'constraints' => ($options['sourceChannel']) ? [new NotEqualTo($options['sourceChannel'])] : [],
        ]);

        if ($options['targetChannel'] !== null) {
            $builder->add('pages', PageCopyPagesType::class, [
                'channel' => $options['sourceChannel'],
                'targetChannel' => $options['targetChannel'],
            ]);

            $builder->add('actions', SaveCancelType::class, [
                'cancel_route' => 'integrated_page_page_index',
                'label' => 'Copy pages',
                'button_class' => '',
                'attr' => [
                    'onclick' => '$(\'#page_copy_action\').val(\'\');',
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['sourceChannel']);
        $resolver->setAllowedTypes('sourceChannel', ['string', 'null']);

        $resolver->setDefault('targetChannel', null);
        $resolver->setAllowedTypes('targetChannel', ['string', 'null']);
    }
}
