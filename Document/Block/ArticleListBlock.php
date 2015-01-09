<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\PageBundle\Document\Block;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * ArticleListBlock document
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Article list block")
 */
class ArticleListBlock extends Block
{
    /**
     * @var SearchSelection
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection")
     * @Type\Field(type="integrated_search_selection_choice")
     */
    protected $searchSelection;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(
     *      type="choice",
     *      options={
     *          "empty_value"="",
     *          "choices"={
     *              "layout1"="Layout 1",
     *              "layout2"="Layout 2"
     *          }
     *      }
     * )
     */
    protected $layout;

    /**
     * @var int
     * @ODM\Int
     * @Type\Field(
     *      type="integer",
     *      options={
     *          "attr"={
     *              "min"=0
     *          }
     *      }
     * )
     */
    protected $maxNrOfItems = 10;

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
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxNrOfItems()
    {
        return $this->maxNrOfItems;
    }

    /**
     * @param int $maxNrOfItems
     * @return $this
     */
    public function setMaxNrOfItems($maxNrOfItems)
    {
        $this->maxNrOfItems = (int) $maxNrOfItems;
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
}
