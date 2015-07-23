<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type\Grid;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\Form\EventListener\ItemOrderListener;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ColumnType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('size', 'hidden');

        $builder->add('items', 'collection', [
            'type'         => new ItemType($this->dm),
            'allow_add'    => true,
            'allow_delete' => true,
            'prototype'    => false,
        ]);

        $builder->addEventSubscriber(new ItemOrderListener());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Integrated\Bundle\PageBundle\Document\Page\Grid\Column',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_grid_column';
    }
}
