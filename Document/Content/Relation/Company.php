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

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Bundle\ContentBundle\Document\Content\File;

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
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\File")
     * @Type\Field(type="integrated_image_choice")
     */
    protected $logo;

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
     * @return File
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the logo of the document
     *
     * @param File $logo
     * @return $this
     */
    public function setLogo(File $logo)
    {
        $this->logo = $logo;
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