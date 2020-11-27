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
use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

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
        $channel = $this->channelContext->getChannel();

        if ($page = $builder->getData()) {
            if ($page->getChannel()) {
                $channel = $page->getChannel();
            }
        }

        $builder->add('channel', ChannelChoiceType::class, [
            'return_object' => true,
            'disabled' => $page && $page->getChannel(),
            'data' => $channel,
        ]);

        $builder->add('title', TextType::class);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);

        $builder->add('path', TextType::class, [
            'label' => 'URL',
            'required' => false,
            'constraints' => [
                new NotBlank(),
                new Regex('/^\/$|(\/[a-zA-Z_0-9-\.\/]+)+$/'),
            ],
        ]);

        $builder->add('disabled', CheckboxType::class, [
            'required' => false,
            'attr' => [
                'align_with_widget' => true,
            ],
        ]);

        $formModifier = function (FormInterface $form, ChannelInterface $channel = null) {
            $theme = null === $channel ? 'default' : $this->themeResolver->getTheme($channel);

            $form->add('layout', LayoutChoiceType::class, [
                'theme' => $theme,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $channel = $this->channelContext->getChannel();
                if ($data && $data->getChannel()) {
                    $channel = $data->getChannel();
                }

                $formModifier($event->getForm(), $channel);
            }
        );

        $builder->get('channel')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent(), $event->getForm()->getData());
            }
        );

        $builder->get('path')->addModelTransformer(new CallbackTransformer(
            function ($path) {
                return ltrim($path, '/');
            },
            function ($path) {
                return '/'.$path;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_page_page';
    }
}
