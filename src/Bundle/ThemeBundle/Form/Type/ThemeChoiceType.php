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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ThemeChoiceType extends AbstractType
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choices_as_values' => true
        ]);
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $themes = array_keys($this->themeManager->getThemes());

        return array_combine($themes, $themes);
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
        return 'integrated_theme_theme_choice';
    }
}
