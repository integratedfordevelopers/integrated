<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Bulk;

use Integrated\Bundle\ContentBundle\Bulk\ActionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ActionFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $model = $event->getData();

            if ($model instanceof ActionInterface) {
                if ($fieldsConfig = $model->getFieldsPreBuildConfig()) {
                    foreach ($fieldsConfig as $fieldConfig) {
                        $form->add($fieldConfig['field_name'], $fieldConfig['field_type'], $fieldConfig['field_options']);
                    }
                }
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $model = $form->getData();
            $data = $event->getData();

            if ($model instanceof ActionInterface) {
                if ($fieldsConfig = $model->getFieldsPostBuildConfig($data)) {
                    foreach ($fieldsConfig as $fieldConfig) {
                        if (isset($fieldConfig['field_remove'])) {
                            $form->remove($fieldConfig['field_name']);
                        }
                        $form->add($fieldConfig['field_name'], $fieldConfig['field_type'], $fieldConfig['field_options']);
                    }
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ActionInterface::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intgrated_content_bulk_edit';
    }
}
