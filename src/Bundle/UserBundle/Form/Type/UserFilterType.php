<?php

namespace Integrated\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Integrated\Bundle\UserBundle\Provider\FilterQueryProvider;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserFilterType extends AbstractType
{
    /**
     * @var FilterQueryProvider
     */
    private $filterQueryProvider;

    /**
     * @param FilterQueryProvider $filterQueryProvider
     */
    public function __construct(FilterQueryProvider $filterQueryProvider)
    {
        $this->filterQueryProvider = $filterQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder
            ->add('q', TextType::class, [
                'attr' => ['placeholder' => 'Find by username'],
                'required' => false,
            ])
            ->add('groups', ChoiceType::class, [
                'choices' => $this->filterQueryProvider->getGroupChoices($options['data']),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('scope', ChoiceType::class, [
                'choices' => $this->filterQueryProvider->getScopeChoices($options['data']),
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('data');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_filter';
    }
}
