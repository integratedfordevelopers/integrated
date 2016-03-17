<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Document type Relation\Company
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Company")
 */
class Company extends Relation
{
    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex(sparse=true)
     * @Slug(fields={"name"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var Storage
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage")
     * @Type\Field(type="integrated_image")
     */
    protected $logo;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $website;

    /**
     * Get the name of the document
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the document
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the slug of the document
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get the file of the document
     *
     * @return Storage
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the logo of the document
     *
     * @param Storage $logo
     * @return $this
     */
    public function setLogo(Storage $logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get the website of the document
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the website of the document
     *
     * @param string $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
