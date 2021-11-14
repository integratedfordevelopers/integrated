<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Type;

use Integrated\Common\Form\EventListener\ClickedButtonListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ActionsType extends AbstractType
{
    /**
     * @var array
     */
    protected $buttons;

    /**
     * Constructor.
     *
     * @param array $buttons
     */
    public function __construct(array $buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ClickedButtonListener());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $buttonsNormalizer = function (Options $options, $buttons) {
            $normalized = [];

            foreach ($buttons as $button) {
                if (!isset($this->buttons[$button])) {
                    throw new InvalidOptionsException(sprintf('The value "%s" for the option "%s" is missing a valid button configuration', $button, 'buttons'));
                }

                $normalized[$button] = $this->buttons[$button];
            }

            return $normalized;
        };

        $resolver->setNormalizer('buttons', $buttonsNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType';
    }
}
