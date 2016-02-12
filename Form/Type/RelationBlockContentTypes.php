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
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RelationBlockContentTypes extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];

        $contentTypes = $this->repository->findAll();
        foreach ($contentTypes as $contentType) {
            $choices[] = $contentType;
        }

        $resolver->setDefault('choices', $choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'integrated_select2';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_relation_block_content_types';
    }
}
