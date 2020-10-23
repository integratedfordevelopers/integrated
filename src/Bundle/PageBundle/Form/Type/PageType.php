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
use Integrated\Bundle\PageBundle\Resolver\ThemeResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class PageType extends AbstractType
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var ThemeResolver
     */
    private $themeResolver;

    /**
     * @param ChannelContextInterface $channelContext
     * @param ThemeResolver           $themeResolver
     */
    public function __construct(ChannelContextInterface $channelContext, ThemeResolver $themeResolver)
    {
        $this->channelContext = $channelContext;
        $this->themeResolver = $themeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('channel', ChannelChoiceType::class, [
            'useObject' => true,
            'disabled' => ($builder->getData()->getChannel()) ? true : false,
            'data' => $builder->getData()->getChannel() ?? $this->channelContext->getChannel(),
        ]);

        $builder->add('title', TextType::class);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);

        $builder->add('path', TextType::class, [
            'label' => 'URL',
        ]);

        $builder->add('layout', LayoutChoiceType::class, [
            'theme' => $this->themeResolver->getTheme($builder->getData()->getChannel() ?? $this->channelContext->getChannel() ?? 'default'),
        ]);

        $builder->add('disabled', CheckboxType::class, [
            'required' => false,
            'attr' => [
                'align_with_widget' => true,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_page_page';
    }
}
