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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * ContainerBlock document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Container block")
 */
class ContainerBlock extends Block
{
    /**
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\BlockBundle\Document\Block\Embedded\BlockSize")
     * @Type\Field(
     *      type="integrated_sortable_collection",
     *      options={
     *          "type"="integrated_block_size",
     *          "default_title"="New item",
     *          "allow_add"=true,
     *          "allow_delete"=true
     *      }
     * )
     */
    protected $items;

    /**
     */
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
