<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\ContentType\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Integrated\Common\ContentType\Form\Custom\TypeInterface;
use Integrated\Common\ContentType\Form\Custom\Type\RegistryInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\Field\CustomTransformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CustomType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * CustomType constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', HiddenType::class);

        $builder->add('label', TextType::class, [
            'required' => true,
        ]);

        $return = [];

        /** @var TypeInterface $type */
        foreach ($this->registry->getIterator() as $type) {
            $return[$type->getType()] = $type->getName();
        }

        $builder->add('type', ChoiceType::class, [
            'required' => true,
            'choices' => $return,
        ]);

        $builder->add(
            'required',
            CheckboxType::class,
            [
                'required' => false,
                'attr' => [
                    'align_with_widget' => true,
                ],
            ]
        );

        $builder->addModelTransformer(new CustomTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_field_custom';
    }
}
