<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Integrated\Bundle\UserBundle\Form\DataMapper\UserMapper;
use Integrated\Bundle\UserBundle\Form\EventListener\UserProfileExtensionListener;
use Integrated\Bundle\UserBundle\Form\EventListener\UserProfileOptionalListener;
use Integrated\Bundle\UserBundle\Form\EventListener\UserProfilePasswordListener;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use ReflectionClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserFormType extends AbstractType
{
    /**
     * @var UserManagerInterface
     */
    private $manager;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * Constructor.
     *
     * @param UserManagerInterface    $manager
     * @param EncoderFactoryInterface $encoder
     */
    public function __construct(UserManagerInterface $manager, EncoderFactoryInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoderFactory = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['optional']) {
            $builder->add(
                'enabled',
                Type\CheckboxType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Enable login',
                    'attr' => [
                        'class' => 'login-visible-control',
                        'align_with_widget' => true,
                    ],
                ]
            );

            // this has to be a event listener as data will not be mapped to this
            // field, even if mapped is set to true, when the field value is false.

            $builder->addEventSubscriber(new UserProfileOptionalListener());
        }

        $builder->add('username', Type\TextType::class, [
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3]),
                new Length(['max' => 60]),
            ],
            'attr' => ['autocomplete' => 'off'],
        ]);

        $builder->add('password', Type\PasswordType::class, [
            'mapped' => false,
            'constraints' => [
                new Length(['min' => 6]),
            ],
            'attr' => ['autocomplete' => 'off'],
        ]);

        if (!$options['optional']) {
            $builder->add(
                'enabled',
                Type\CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Enable login',
                    'attr' => [
                        'align_with_widget' => true,
                    ],
                ]
            );
        }

        $builder->add('groups', GroupType::class, [
            'multiple' => true,
            'expanded' => true,
        ]);

        $builder->add('scope', EntityType::class, [
            'class' => Scope::class,
            'choice_label' => 'name',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('Scope')
                    ->addSelect('CASE WHEN Scope.name = :name THEN 1 ELSE 0 END AS HIDDEN sortCondition')
                    ->setParameter('name', 'Integrated')
                    ->orderBy('sortCondition', 'DESC');
            },
        ]);

        $builder->addEventSubscriber(new UserProfilePasswordListener($this->encoderFactory));
        $builder->addEventSubscriber(new UserProfileExtensionListener('integrated.extension.user'));

        if ($options['optional']) {
            $builder->setDataMapper(new UserMapper());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (Options $options, $previous) {
            if (!$options['optional']) {
                return $this->getManager()->create(); // if not optional then it should always return a user
            }

            return function (FormInterface $form) {
                if ($form->has('enabled') && $form->get('enabled')->getData() == false) {
                    return null;
                }

                return $this->getManager()->create();
            };
        };

        $validationGroups = function (Options $options, $previous) {
            if (!$options['optional']) {
                return $previous; // does not need all the processing
            }

            // validation should be disabled when enabled is not checked

            return function (FormInterface $form) use ($previous) {
                $resolve = function (FormInterface $form) use ($previous) {
                    if ($form->has('enabled') && $form->get('enabled')->getData() == false) {
                        return false;
                    }

                    return $previous;
                };

                if (null !== ($groups = $resolve($form))) {
                    return $groups;
                }

                // yeah now we are going to cheat as we don't want to rewrite what is already
                // made by someone else.

                $reflection = new ReflectionClass('Symfony\Component\Form\Extension\Validator\Constraints\FormValidator');

                $method = $reflection->getMethod('getValidationGroups');
                $method->setAccessible(true);

                return $method->invoke(null, $form->getParent());
            };
        };

        $resolver->setDefault('data_class', $this->getManager()->getClassName());
        $resolver->setDefault('empty_data', $emptyData);
        $resolver->setDefault('validation_groups', $validationGroups);

        // everything can be left empty if enabled is not checked

        $resolver->setDefault('optional', false);
        $resolver->setDefault('attr', ['class' => 'integrated-user-form']);
        $resolver->setAllowedTypes('optional', ['bool']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_user_form';
    }

    /**
     * @return UserManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
