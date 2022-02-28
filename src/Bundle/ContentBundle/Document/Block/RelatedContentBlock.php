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

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Related content block document.
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 *
 * @Type\Document("Related Content block")
 */
class RelatedContentBlock extends Block
{
    /**
     * Show items which have the current document linked.
     */
    public const SHOW_USED_BY = 1;

    /**
     * Show items which share linked items with the current document.
     */
    public const SHOW_LINKED = 2;

    /**
     * Show items linked by the current document.
     */
    public const SHOW_LINKED_BY = 3;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field
     */
    protected $publishedTitle;

    /**
     * @var int
     * @Assert\NotBlank
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\ChoiceType",
     *     options={
     *         "choices"={
     *             "Show items which have the current document linked"="1",
     *             "Show items which share linked items with the current document"="2",
     *             "Show items linked by the current document"="3",
     *          },
     *     }
     * )
     */
    protected $typeBlock;

    /**
     * @var Relation
     * @Type\Field(
     *      type="Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType",
     *      options={
     *          "class"="IntegratedContentBundle:Relation\Relation",
     *          "choice_label"="name",
     *          "placeholder"=""
     *      }
     * )
     */
    protected $relation;

    /**
     * @var string
     * @Assert\NotBlank
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\ChoiceType",
     *     options={
     *         "choices"={
     *             "Publication date"="publishTime.startDate",
     *             "Title"="title",
     *             "Linked order"="linked"
     *          },
     *     }
     * )
     */
    protected $sortBy;

    /**
     * @var string
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\ChoiceType",
     *     options={
     *         "choices"={
     *             "asc"="asc",
     *             "desc"="desc"
     *          },
     *     }
     * )
     */
    protected $sortDirection;

    /**
     * @var int
     * @Assert\Length(min=0)
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\IntegerType",
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
     * @Assert\Length(min=0)
     * @Type\Field(
     *      type="Symfony\Component\Form\Extension\Core\Type\IntegerType",
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
     * @Type\Field(
     *     type="Integrated\Bundle\ContentBundle\Form\Type\ContentTypeChoice",
     *     options={
     *         "required"=false
     *     }
     * )
     */
    protected $contentTypes;

    /**
     * Get the block type.
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
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param string $sortDirection
     *
     * @return $this
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;

        return $this;
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
