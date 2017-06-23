<?php

/**
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Bundle\ContentBundle\Document\Bulk\Action\RelationAction;
use Integrated\Bundle\ContentBundle\Form\EventListener\BulkRelationActionListener;
use Integrated\Bundle\ContentBundle\Form\Type\Fields\ReferencesChoiceType;
use Integrated\Common\Content\Relation\RelationInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class BulkRelationActionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relation = $options['relation'];

        $builder->add(
            'references',
            ReferencesChoiceType::class,
            [
                'label' => $options['label'],
                'attr' => [
                    'data-id' => $relation->getId(),

                    'class' => 'relation-items',
                ],
            ]
        );

        $builder->addEventSubscriber(new BulkRelationActionListener());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', RelationAction::class)
            ->setRequired(['relation', 'handler', 'label'])
            ->setAllowedTypes('relation', RelationInterface::class)
            ->setAllowedTypes('handler', 'string')
            ->setAllowedTypes('label', 'string')
        ;
    }
}
