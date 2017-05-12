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

use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\FormTypeBundle\Form\Type\CollectionType;
use Integrated\Bundle\FormTypeBundle\Validator\Constraints\NotEmptyCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ActionsFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('actions', CollectionType::class, [
            'label' => false,
            'entry_type' => ActionFormType::class,
            'allow_delete' => true,
            'constraints' => [
                new NotEmptyCollection()
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BulkAction::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intgrated_content_bulk_configure';
    }
}
