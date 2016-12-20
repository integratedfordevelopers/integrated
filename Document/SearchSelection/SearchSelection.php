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

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

/**
 * SearchSelection document.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelection
{
    /**
     * @var string
     * @Slug(fields={"title"})
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var bool
     */
    protected $public = false;

    /**
     * @var int
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
     * @param string $key
     * @return array
     */
    public function getFilter($key)
    {
        return isset($this->filters[$key]) ? $this->filters[$key] : null;
    }

    /**
     * @param string $key
     * @param string $value
     * @return SearchSelection
     */
    public function setFilter($key, $value)
    {
        $this->filters[$key] = $value;
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
