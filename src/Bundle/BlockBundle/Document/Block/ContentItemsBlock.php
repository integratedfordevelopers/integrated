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
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @Type\Document("Content items block")
 */
class ContentItemsBlock extends Block
{
    /**
     * @var ArrayCollection
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\ContentChoiceType")
     */
    protected $items;

    /**
     * General object init.
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
        $items = $this->items->toArray();

        return $items;
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
        return 'content_items';
    }
}
