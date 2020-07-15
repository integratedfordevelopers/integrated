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

use Integrated\Bundle\ThemeBundle\Entity\Scraper\Block;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScraperBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'disabled' => true,
        ]);

        $builder->add('mode', ChoiceType::class, [
            'choices' => [
                'Ignore' => Block::MODE_IGNORE,
                'Append' => Block::MODE_APPEND,
                'Replace' => Block::MODE_REPLACE,
                'Inner replace' => Block::MODE_REPLACE_INNER,
            ],
        ]);

        $builder->add('selector', TextType::class, [
            'required' => false,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
        ]);
    }
}
