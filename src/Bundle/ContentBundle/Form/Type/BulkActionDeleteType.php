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

use Integrated\Bundle\ContentBundle\Bulk\DeleteHandler;
use Integrated\Bundle\ContentBundle\Document\Bulk\Action\DeleteAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulkActionDeleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'removeReferences',
            ChoiceType::class,
            [
                'label' => $options['label'],
                'multiple' => false,
                'choices' => ['Skip items which are in use' => false, 'Remove references before deletion' => true],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', DeleteAction::class)
            ->setDefault('empty_data', function (Options $options) {
                $action = new DeleteAction(DeleteHandler::class);

                return $action;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_bulk_action_delete';
    }
}
