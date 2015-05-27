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

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use Integrated\Bundle\UserBundle\Validator\Constraints\UniqueGroup;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupFormType extends AbstractType
{
    /**
     * @var GroupManagerInterface
     */
    private $manager;

    /**
     * @param GroupManagerInterface $manager
     */
    public function __construct(GroupManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'required' => false,
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 3])
            ]
        ]);

        $builder->add('roles', 'user_role_choice');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('empty_data', function(FormInterface $form) { return $this->getManager()->create(); });
        $resolver->setDefault('data_class', $this->getManager()->getClassName());

        $resolver->setDefault('constraints', new UniqueGroup($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_user_group_form';
    }

    /**
     * @return GroupManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
