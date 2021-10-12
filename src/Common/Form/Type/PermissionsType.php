<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use Integrated\Bundle\UserBundle\Form\Type\GroupType;
use Integrated\Common\Form\DataTransformer\PermissionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionsType extends AbstractType
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->getTransformer());

        $builder->add('read', GroupType::class, [
            'required' => false,
            'multiple' => true,
            'label' => $options['read-label'],
            'attr' => [
                'class' => 'select2',
                'data-placeholder' => $options['read-placeholder'],
            ],
        ]);

        $builder->add('write', GroupType::class, [
            'required' => false,
            'multiple' => true,
            'label' => $options['write-label'],
            'attr' => [
                'class' => 'select2',
                'data-placeholder' => $options['write-placeholder'],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = function (FormInterface $form) {
            return new ArrayCollection();
        };

        $resolver->setDefault('empty_data', $emptyData);
        $resolver->setDefault('label', false);

        $resolver->setDefault('read-label', 'Read');
        $resolver->setDefault('write-label', 'Write');

        $resolver->setDefault('read-placeholder', null);
        $resolver->setDefault('write-placeholder', null);
    }

    /**
     * @return PermissionTransformer
     */
    protected function getTransformer()
    {
        return new PermissionTransformer($this->repository);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_permissions';
    }
}
