<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\Type;

use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowFormType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * WorkflowFormType constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', 'textarea', ['required' => false]);

        $builder->add('state', 'workflow_state', ['label' => 'Workflow status', 'workflow' => $options['workflow']]);

        $builder->add('workflow', 'hidden', ['data' => $options['workflow'], 'attr' => ['class' => 'workflow-hidden']]);

        $builder->add(
            'assigned',
            'integrated_select2',
            [
                'empty_value' => 'Not Assigned',
                'empty_data'  => null,
                'required' => false,
                'attr' => ['class' => 'assigned-choice'],
                'choices' => $this->getAssigned(),
            ]
        );

        $builder->add('deadline', 'integrated_datetime');
    }

    /**
     * @return array
     */
    public function getAssigned()
    {
        $userRepository = $this->userManager->getRepository();
        $users = [];

        /** @var User $item */
        foreach ($userRepository->findAll() as $item) {
            $users[$item->getId()] = $item->getUsername();
        }

        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('workflow');
        $resolver->setAllowedTypes('workflow', ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_workflow';
    }
}
