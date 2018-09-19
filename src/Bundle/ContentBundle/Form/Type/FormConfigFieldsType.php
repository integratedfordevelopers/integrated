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

use Integrated\Bundle\ContentBundle\Form\EventListener\FormConfigFieldsListener;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormConfigFieldsType extends AbstractType
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('available', $builder->create($builder->getName(), FormType::class)->getForm());
        $builder->setAttribute('custom', $builder->create('__name__', FormConfigFieldType::class, ['content_type' => $options['content_type']])->getForm());

        $builder->addEventSubscriber(new FormConfigFieldsListener($this->provider->getFields($options['content_type'])));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = $options['content_type'];
        $view->vars['available'] = $form->getConfig()->getAttribute('available')->createView($view->parent);
        $view->vars['custom'] = $form->getConfig()->getAttribute('custom')->createView($view);
    }

    /**
     * {@inheritdoc}
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
        return 'integrated_content_form_config_fields';
    }
}
