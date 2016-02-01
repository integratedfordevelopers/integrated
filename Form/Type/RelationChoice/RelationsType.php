<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\FormTypeBundle\Form\Type\RelationChoice\EventListener\AddRelationFieldsSubscriber;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationsType extends AbstractType
{
    /**
     * @var AddRelationFieldsSubscriber
     */
    protected $subscriber;

    /**
     * @param AddRelationFieldsSubscriber $subscriber
     */
    public function __construct(AddRelationFieldsSubscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => []
        ]);

        $resolver->setRequired(['relations']);

        $resolver->setAllowedTypes('relations', ['array']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_relations_choice';
    }

}
