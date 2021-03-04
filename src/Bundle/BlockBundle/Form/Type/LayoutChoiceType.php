<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Form\Type;

use Integrated\Bundle\BlockBundle\Locator\LayoutLocator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LayoutChoiceType extends AbstractType
{
    /**
     * @var LayoutLocator
     */
    protected $locator;

    /**
     * @param LayoutLocator $locator
     */
    public function __construct(LayoutLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_label' => function ($value) {
                return $value;
            },
            'choices' => function (Options $options) {
                return $this->getChoiceList($options['type']);
            },
        ]);

        $resolver->setRequired([
            'type',
        ]);
    }

    /**
     * @param $type
     *
     * @return array
     */
    protected function getChoiceList($type)
    {
        $layouts = $this->locator->getLayouts($type);

        sort($layouts);

        return $layouts;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_block_layout_choice';
    }
}
