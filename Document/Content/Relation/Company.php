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

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Bundle\ContentBundle\Document\Content\File;

/**
 * Document type Relation\Company
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @Type\Document("Company")
 */
class Company extends Relation
{
    /**
     * @var string
     * @Type\Field
     */
    protected $name;

    /**
     * @var string
     * @Slug(fields={"name"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var File
     * @Type\Field(type="integrated_image_choice")
     */
    protected $logo;

    /**
     * @var string
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
