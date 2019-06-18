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

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageCopyBlockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('operation', ChoiceType::class, [
            'required' => true,
            'expanded' => true,
            'choices' => [
                'Re-use' => '',
                'Clone' => 'clone',
            ],
            'attr' => [
                'style' => 'inline',
            ]
        ]);

        $builder->add('newBlockId', TextType::class, [
            'required' => false,
            'label' => false,
            'attr' => [
                'data-proposed-block-id' => str_replace($options['channel'], $options['targetChannel'], $options['block']->getId()),
                'style' => 'height: 26px;',
                //'disabled' => ($builder->get('newBlockId')->getData() != '') ? false : true,
            ],
//            $builder->get('newBlockId')->getData()
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            dump($data);

            if ($data['operation'] != 'clone') {
                $options = $form->get('newBlockId')->getConfig()->getOptions();
                $options['attr']['disabled'] = true;

                $form->add('newBlockId', TextType::class, $options);
            }
        });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['block', 'channel', 'targetChannel']);
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('channel', 'string');
        $resolver->setAllowedTypes('targetChannel', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge($view->vars, [
            'block' => $options['block'],
        ]);
    }
}
