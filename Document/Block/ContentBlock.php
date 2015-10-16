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

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * Content block document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Content block")
 */
class ContentBlock extends Block
{
    /**
     * @var SearchSelection
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection")
     * @Type\Field(type="integrated_search_selection_choice")
     */
    protected $searchSelection;

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
     * @var string
     * @ODM\String
     * @Type\Field(
     *      type="text",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $readMoreUrl;

    /**
     * @var array
     * @ODM\Collection
     * @Type\Field(
     *      type="bootstrap_collection",
     *      options={
     *          "allow_add"=true,
     *          "allow_delete"=true,
     *          "required"=false,
     *      }
     * )
     */
    protected $facetFields = [];

    /**
     * @var string
     * @ODM\String
     * @Type\Field(options={"required"=false})
     */
    protected $publishedTitle;

    /**
     * @ODM\Boolean
     * @Type\Field(
     *      type="checkbox",
     *      options={
     *          "required"=false
     *      }
     * )
     */
    protected $useTitle;

    /**
     * @return SearchSelection
     */
    public function getSearchSelection()
    {
        return $this->searchSelection;
    }

    /**
     * @param SearchSelection $searchSelection
     * @return $this
     */
    public function setSearchSelection(SearchSelection $searchSelection = null)
    {
        $this->searchSelection = $searchSelection;
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
     * @return $this
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
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
     * @return $this
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
        return $this;
    }

    /**
     * @return string
     */
    public function getReadMoreUrl()
    {
        return $this->readMoreUrl;
    }

    /**
     * @param string $readMoreUrl
     * @return $this
     */
    public function setReadMoreUrl($readMoreUrl)
    {
        $this->readMoreUrl = $readMoreUrl;
        return $this;
    }

    /**
     * @return array
     */
    public function getFacetFields()
    {
        return $this->facetFields;
    }

    /**
     * @param array $facetFields
     * @return $this
     */
    public function setFacetFields(array $facetFields = [])
    {
        $this->facetFields = $facetFields;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'content';
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

    public function getValidTitle()
    {
        if ($this->useTitle) {
            return $this->useTitle;
        }

        return $this->publishedTitle !== null ? $this->publishedTitle : $this->title;
    }

    /**
     * @return mixed
     */
    public function getUseTitle()
    {
        return $this->useTitle;
    }

    /**
     * @param mixed $useTitle
     */
    public function setUseTitle($useTitle)
    {
        $this->useTitle = $useTitle;
    }
}
