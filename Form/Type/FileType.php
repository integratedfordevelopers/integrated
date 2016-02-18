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

use ArrayObject;

use Integrated\Bundle\StorageBundle\Form\EventSubscriber\FileEventSubscriber;

use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
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
        $constraints = new ArrayObject();
        $constraintsNormalizer = function(Options $options, $value) use ($constraints)  {
            $constraints->exchangeArray(is_object($value) ? [$value] : (array) $value);
            return [];
        };

        $constraintsFileNormalizer = function(Options $options) use ($constraints)  {
            return $constraints->getArrayCopy();
        };

        // The field might not be required in the integrated content type
        $resolver->setDefaults([
            'compound' => true,
            'required' => false,
            'constraints_file' => [],
        ]);

        $resolver->setNormalizer('constraints', $constraintsNormalizer);
        $resolver->setNormalizer('constraints_file', $constraintsFileNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', [
            'data_class' => 'Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage',
            'required' => false,
            'mapped' => false,
            'constraints' => $options['constraints_file']
        ]);

        $builder->add('remove', 'checkbox', [
            'mapped' => false,
            'required' => false,
        ]);

        $builder->addEventSubscriber(new FileEventSubscriber($this->manager, $this->decision));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_file';
    }
}
