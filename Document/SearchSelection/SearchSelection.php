<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\SearchSelection;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * SearchSelection document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document(collection="search_selection", repositoryClass="SearchSelectionRepository")
 */
class SearchSelection
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ODM\String
     * @ODM\Index
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex(sparse=true)
     * @Slug(fields={"title"})
     */
    protected $slug;

    /**
     * @var array
     * @ODM\Hash
     */
    protected $filters = [];

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $public = false;

    /**
     * @var int
     * @ODM\Int
     */
    protected $userId;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return SearchSelection
     */
    public function setFilters(array $filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     * @return SearchSelection
     */
    public function setPublic($public)
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return SearchSelection
     */
    public function setUserId($userId)
    {
        $this->userId = (int) $userId;
        return $this;
    }
}
