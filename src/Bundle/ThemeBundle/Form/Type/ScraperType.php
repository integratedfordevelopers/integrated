<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Form\Type;

use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\FormTypeBundle\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ScraperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class);

        $builder->add('channelId', ChannelChoiceType::class, ['label' => 'Channel']);

        $builder->add('templateName', TextType::class);

        $builder->add('url', TextType::class, ['label' => 'Scraper page URL']);

        $builder->add('blocks', CollectionType::class, [
            'entry_type' => ScraperBlockType::class,
        ]);

        $builder->add('actions', \Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ],
        ]);
    }
}
