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

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\AuthorTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AuthorType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $mr;

    /**
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AuthorTransformer($this->mr);

        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_author';
    }
}
