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

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Facet block document.
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
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
     *      options={
     *          "class"="IntegratedContentBundle:Block\ContentBlock",
     *          "choice_label"="title",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $block;

    /**
     * @var ArrayCollection
     * @Type\Field(
     *      type="Integrated\Bundle\FormTypeBundle\Form\Type\CollectionType",
     *      options={
     *          "entry_type"="Integrated\Bundle\FormTypeBundle\Form\Type\EmbeddedDocumentType",
     *          "entry_options"={
     *              "data_class"="Integrated\Bundle\ContentBundle\Document\Block\Embedded\FacetField"
     *          },
     *          "allow_add"=true,
     *          "allow_delete"=true
     *      }
     * )
     */
    protected $fields;

    public function __construct()
    {
        parent::__construct();
        $this->fields = new ArrayCollection();
    }

    /**
     * @return ContentBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param ContentBlock $block
     *
     * @return $this
     */
    public function setBlock(ContentBlock $block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * @return \Integrated\Bundle\ContentBundle\Document\Block\Embedded\FacetField[]
     */
    public function getFields()
    {
        return $this->fields->toArray();
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = new ArrayCollection($fields);

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
