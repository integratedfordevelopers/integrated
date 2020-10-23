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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;

class PageFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $session = new Session();

        $builder->add('q', TextType::class, [
            'label' => 'Search filter',
            'required' => false,
            'data' => $session->get('pagefilter_q', ''),
        ]);

        $builder->add('pagetype', ChoiceType::class, [
            'label' => 'Page type',
            'choices' => [
                '' => '',
                'Static pages' => 'page',
                'Content type pages' => 'contenttype',
            ],
            'required' => false,
            'data' => $session->get('pagefilter_pagetype', ''),
        ]);

        $builder->add('channel', ChannelChoiceType::class, [
            'label' => 'Channel',
            'required' => false,
            'data' => $session->get('pagefilter_channel', ''),
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Filter',
        ]);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function ($event) use ($session) {
                $data = $event->getData();

                $session->set('pagefilter_q', $data['q']);
                $session->set('pagefilter_channel', $data['channel']);
                $session->set('pagefilter_pagetype', $data['pagetype']);
            }
        );
    }
}
