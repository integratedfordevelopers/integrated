<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable;

/**
 * Document type Taxonomy
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document(collection="content")
 * @Type\Document("Taxonomy")
 */
class Taxonomy extends Content
{
    /**
     * @var Translatable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_text")
     */
    protected $title;

    /**
     * @var Translable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_textarea")
     */
    protected $description;

    /**
     * Get the title of the document
     *
     * @return Translatable
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param Translatable $title
     * @return $this
     */
    public function setTitle(Translatable $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the description of the document
     *
     * @return Translatable
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description of the document
     *
     * @param Translatable $description
     * @return $this
     */
    public function setDescription(Translatable $description)
    {
        $this->description = $description;
        return $this;
    }
}