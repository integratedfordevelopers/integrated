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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

use Integrated\Bundle\BlockBundle\Locator\LayoutLocator;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LayoutChoiceType extends AbstractType
{
    /**
     * @var LayoutLocator
     */
    private $locator;

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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'choice_list' => function(Options $options) { return $this->getChoiceList($options['type']); },
        ]);

        $resolver->setRequired([
            'type',
        ]);
    }

    /**
     * @param string $type
     * @return ChoiceList
     */
    protected function getChoiceList($type)
    {
        $layout = $this->locator->getLayouts($type);

        return new ChoiceList($layout, $layout);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_layout_choice';
    }
}