<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Document\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * ContainerBlock document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Container block")
 */
class ContainerBlock extends Block
{
    /**
     * @var ArrayCollection
     * @Type\Field(
     *      type="Integrated\Bundle\FormTypeBundle\Form\Type\SortableCollectionType",
     *      options={
     *          "entry_type"="Integrated\Bundle\BlockBundle\Form\Type\BlockSizeType",
     *          "default_title"="New block",
     *          "allow_add"=true,
     *          "allow_delete"=true
     *      }
     * )
     */
    protected $items;

    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items->toArray();
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = new ArrayCollection($items);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'container';
    }
}
