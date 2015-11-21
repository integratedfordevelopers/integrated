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

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * Content block document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Type\Document("Content block")
 */
class ContentBlock extends Block
{
    /**
     * @var SearchSelection
     * @Type\Field(type="integrated_search_selection_choice")
     */
    protected $searchSelection;

    /**
     * @var int
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
}
