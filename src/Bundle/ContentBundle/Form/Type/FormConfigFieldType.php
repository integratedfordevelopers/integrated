<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\FormConfigFieldTransformer;
use Integrated\Bundle\ContentBundle\Form\EventListener\FormConfigFieldOptionsListener;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormConfigFieldType extends AbstractType
{
    /**
     * @var FormConfigFieldProviderInterface
     */
    private $provider;

    /**
     * @param FormConfigFieldProviderInterface $provider
     */
    public function __construct(FormConfigFieldProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new FormConfigFieldTransformer($this->provider->getFields($options['content_type'])));

        $builder->add('id', HiddenType::class);
        $builder->add('type', HiddenType::class);
        $builder->add('required', CheckboxType::class, ['required' => false]);

        $builder->addEventSubscriber(new FormConfigFieldOptionsListener());
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('content_type');
        $resolver->setAllowedTypes('content_type', [ContentTypeInterface::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_form_config_field';
    }
}
