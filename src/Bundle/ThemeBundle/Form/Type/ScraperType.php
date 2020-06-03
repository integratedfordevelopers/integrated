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

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;
use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\FormTypeBundle\Form\Type\CollectionType;
use Integrated\Bundle\ThemeBundle\Entity\Scraper\Block;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ScraperType extends AbstractType
{

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);

        $builder->add('channelId', ChannelChoiceType::class);

        $builder->add('templateName', TextType::class);

        $builder->add('url', TextType::class);

        $builder->add('blocks', BootstrapCollectionType::class, [
            'entry_type' => ScraperBlockType::class,
        ]);

        $builder->add('actions', FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
            ],
        ]);
    }

}
