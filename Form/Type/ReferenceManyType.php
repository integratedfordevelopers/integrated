<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Metadata\MetadataFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ReferenceManyType extends AbstractType
{
    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $metadata = $this->metadataFactory->getMetadata($options['data_class']);

        foreach ($metadata->getFields() as $field) {
            $builder->add($field->getName(), $field->getType(), $field->getOptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_reference_many';
    }
}
