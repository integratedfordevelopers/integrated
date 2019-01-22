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
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Common\Security\PermissionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

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
     * @var ContentTypeManager
     */
    private $contentTypeManager;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @param ObjectRepository $repository
     * @param ContentTypeManager $contentTypeManager
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(ObjectRepository $repository, ContentTypeManager $contentTypeManager, AuthorizationChecker $authorizationChecker)
    {
        $this->repository = $repository;
        $this->contentTypeManager = $contentTypeManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];
        foreach ($this->contentTypeManager->getAll() as $contentType) {
            if (!$this->authorizationChecker->isGranted(PermissionInterface::WRITE, $contentType)) {
                continue;
            }

            $choices[$contentType->getName()] = $contentType->getId();
        }

        ksort($choices);

        $resolver
            ->setDefaults([
                'multiple' => true,
                'choices' => $choices,
                'attr' => [
                    'class' => 'basic-multiple',
                ],
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
