<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\Type;

use Integrated\Bundle\StorageBundle\Form\DataTransformer\FileDataTransformer;
use Integrated\Bundle\StorageBundle\Form\EventSubscriber\FileEventSubscriber;
use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileType extends AbstractType
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var DecisionInterface
     */
    protected $decision;

    /**
     * @param ManagerInterface $manager
     * @param DecisionInterface $decision
     */
    public function __construct(ManagerInterface $manager, DecisionInterface $decision)
    {
        $this->manager = $manager;
        $this->decision = $decision;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // The field might not be required in the integrated content type
        $resolver->setDefaults([
            'required' => false,
            'data_class' => 'Integrated\Bundle\ContentBundle\Document\Storage\Embedded\Storage',
            'empty_data' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FileDataTransformer());
        $builder->addEventSubscriber(new FileEventSubscriber($this->manager, $this->decision));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }
}
