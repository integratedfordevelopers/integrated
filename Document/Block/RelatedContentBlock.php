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

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * Related content block document
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 *
 * @ODM\Document
 * @Type\Document("Related Content block")
 */
class RelatedContentBlock extends Block
{

    /**
     * Show items which have the current document linked
     */
    const SHOW_USED_BY = 1;

    /**
     * Show items which share linked items with the current document
     */
    const SHOW_LINKED = 2;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     * @Type\Field
     */
    protected $publishedTitle;

    /**
     * @var int
     * @ODM\Int
     * @Assert\NotBlank
     * @Type\Field(
     *     type="choice",
     *     options={
     *         "choices"={
     *             1="Show items which have the current document linked",
     *             2="Show items which share linked items with the current document",
     *          },
     *     }
     * )
     */
    protected $typeBlock;

    /**
     * @var ContentBlock
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Relation\Relation")
     * @Type\Field(
     *      type="document",
     *      options={
     *          "class"="IntegratedContentBundle:Relation\Relation",
     *          "property"="name",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $relation;

    /**
     * @var string
     * @ODM\String)
     * @Assert\NotBlank
     * @Type\Field(
     *     type="choice",
     *     options={
     *         "choices"={
     *             "publishTime.startDate"="Publication date"
     *          },
     *     }
     * )
     */
    protected $sortBy;

    /**
     * @var int
     * @ODM\Int
     * @Assert\Length(min=0)
     * @Type\Field(
     *      type="integer",
     *      options={
     *          "attr"={
     *              "min"=0
     *          }
     *      }
     * )
     */
    protected $itemsPerPage = 10;

    /**
     * @var int
     * @ODM\Int
     * @Assert\Length(min=0)
     * @Type\Field(
     *      type="integer",
     *      options={
     *          "required"=false,
     *          "attr"={
     *              "min"=0,
     *          }
     *      }
     * )
     */
    protected $maxItems;

    /**
     * @var array
     * @ODM\Collection
     * @Type\Field(type="integrated_content_type_choice")
     */
    protected $contentTypes;

    /**
     * Get the block type
     *
     * @return string
     */
    public function getType()
    {
        return 'related_content';
    }

    /**
     * @return string
     */
    public function getPublishedTitle()
    {
        return $this->publishedTitle;
    }

    /**
     * @param string $publishedTitle
     */
    public function setPublishedTitle($publishedTitle)
    {
        $this->publishedTitle = $publishedTitle;
    }

    /**
     * @return string
     */
    public function getTypeBlock()
    {
        return $this->typeBlock;
    }

    /**
     * @param string $typeBlock
     */
    public function setTypeBlock($typeBlock)
    {
        $this->typeBlock = $typeBlock;
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Relation $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * @param array $contentTypes
     */
    public function setContentTypes($contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
}
