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

use Integrated\Bundle\FormTypeBundle\Form\Type\DateTimeType;
use Integrated\Bundle\FormTypeBundle\Form\Type\Select2Type;
use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\WorkflowDefaultDataListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * WorkflowFormType constructor.
     *
     * @param UserManager  $userManager
     * @param TokenStorage $tokenStorage
     */
    public function __construct(UserManager $userManager, TokenStorage $tokenStorage)
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('state', WorkflowStateType::class, ['label' => 'Workflow status', 'workflow' => $options['workflow']]);

        $builder->add('comment', TextareaType::class, ['required' => false, 'attr' => ['class' => 'comment']]);

        $builder->add('workflow', HiddenType::class, ['data' => $options['workflow'], 'attr' => ['class' => 'workflow-hidden']]);

        $builder->add('contentType', HiddenType::class, ['data' => $options['contentType'], 'attr' => ['class' => 'content-type-hidden']]);

        $builder->add(
            'assigned',
            Select2Type::class,
            [
                'placeholder' => 'Not Assigned',
                'required' => false,
                'attr' => ['class' => 'assigned-choice'],
                'choices' => $this->getAssigned(),
            ]
        );

        $builder->add('deadline', DateTimeType::class, ['attr' => ['class' => 'form-control deadline']]);

        $builder->addEventSubscriber(new WorkflowDefaultDataListener($this->tokenStorage));
    }

    /**
     * @return array
     */
    public function getAssigned()
    {
        $builder = $this->userManager->createQueryBuilder();
        $query = $builder->getQuery();

        $users = [];

        foreach ($query->getArrayResult() as $item) {
            $users[$item['username']] = $item['id'];
        }

        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('workflow');
        $resolver->setRequired('contentType');
        $resolver->setAllowedTypes('workflow', ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']);
        $resolver->setAllowedTypes('contentType', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_workflow';
    }
}
