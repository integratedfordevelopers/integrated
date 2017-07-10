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

use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkSelectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selection', ChoiceType::class, [
            'label' => false,
            'choices' => $options['content'],
            'choices_as_values' => true,
            'error_bubbling' => true,
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('content')
            ->setAllowedTypes('content', 'array')
            ->setAllowedValues('content', function (array $content) {
                foreach ($content as $item) {
                    if (!$item instanceof ContentInterface) {
                        return false;
                    }
                }

                return true;
            });

        $resolver->setDefault('data_class', BulkAction::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'intgrated_content_bulk_select';
    }
}
