<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Block;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * Facet block document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Facet block")
 */
class FacetBlock extends Block
{
    /**
     * @var ContentBlock
     * @Type\Field(
     *      type="document",
     *      options={
     *          "class"="IntegratedContentBundle:Block\ContentBlock",
     *          "property"="title",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $block;

    /**
     * @var string
     * @Type\Field(type="text")
     */
    protected $field;

    /**
     * @return ContentBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param ContentBlock $block
     * @return $this
     */
    public function setBlock(ContentBlock $block)
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'facet';
    }
}
