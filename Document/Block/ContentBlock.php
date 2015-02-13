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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * ContentBlock document
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

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'content';
    }
}