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

use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\CustomField;
use Integrated\Common\ContentType\ContentTypeInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomFieldsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ContentTypeInterface $metadata */
        $contentType = $options['contentType'];

        foreach ($contentType->getFields() as $field) {
            if (!$field instanceof CustomField) {
                continue;
            }

            $options = $field->getOptions();
            $builder->add(
                $field->getName(),
                $field->getType(),
                $options + ['constraints' => !empty($options['required']) ? [new NotBlank()] : []]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['contentType'])
            ->setAllowedTypes(['contentType' => ContentTypeInterface::class])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_custom_fields';
    }
}
