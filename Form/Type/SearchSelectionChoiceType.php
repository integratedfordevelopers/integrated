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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\SearchSelectionChoiceTransformer;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionChoiceType extends AbstractType
{
    /**
     * @var \Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelectionRepository
     */
    private $repository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param DocumentManager       $manager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(DocumentManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->repository = $manager->getRepository('IntegratedContentBundle:SearchSelection\SearchSelection');
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SearchSelectionChoiceTransformer($this->repository));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];
        if ($user = $this->getUser()) {
            foreach ($this->repository->findPublicByUserId($user->getId()) as $selection) {
                /** @var \Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection $selection */
                $choices[$selection->getId()] = $selection->getTitle();
            }
        }


        $resolver->setDefaults([
            'choices' => $choices,
            'choices_as_value' => true,
            'choice_label' => function($value) use ($choices) {
                return !empty($choices[$value]) ? $choices[$value] : '';
            },
            'placeholder' => ''
        ]);
    }

    /**
     * @return UserInterface|null
     */
    private function getUser()
    {
        if ($token = $this->tokenStorage->getToken()) {
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                return $user;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_search_selection_choice';
    }
}
