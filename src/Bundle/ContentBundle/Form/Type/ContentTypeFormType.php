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

use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MetadataInterface $metadata */
        $metadata = $options['metadata'];

        $builder
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('channels', ContentTypeChannelsType::class, ['property_path' => 'options[channels]'])
        ;

        foreach ($metadata->getOptions() as $option) {
            $ype = $builder->create('options_'.$option->getName(), $option->getType(), ['label' => ucfirst($option->getName())] + $option->getOptions())
                ->setPropertyPath('options['.$option->getName().']');

            $builder->add($ype);
        }

        $builder->add('permissions', PermissionsType::class, [
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['metadata']);
        $resolver->setAllowedTypes('metadata', 'Integrated\\Common\\Form\\Mapping\\MetadataInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type';
    }
}
