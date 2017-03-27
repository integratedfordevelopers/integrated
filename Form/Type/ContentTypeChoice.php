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

use Doctrine\Common\Persistence\ObjectRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ContentTypeChoice extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];
        $contentTypes = $this->repository->findAll();
        foreach ($contentTypes as $contentType) {
            $choices[$contentType->getName()] = $contentType->getId();
        }

        $resolver
            ->setDefaults([
                'multiple' => true,
                'choices' => $choices,
                'choices_as_values' => true,
                'attr' => [
                    'class' => 'basic-multiple'
                ]
            ])
        ;
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
        return 'integrated_content_type_choice';
    }
}
