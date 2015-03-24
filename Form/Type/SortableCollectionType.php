<?php

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SortableCollectionType extends BootstrapCollectionType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'sortable_collection';
    }
}
