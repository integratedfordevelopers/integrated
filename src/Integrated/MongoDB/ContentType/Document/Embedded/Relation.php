<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\ContentType\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\MongoDB\ContentType\Document\ContentType;

/**
 * Embedded document Relation
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Relation
{
    /**
     * @var ContentType
     * @ODM\ReferenceOne(targetDocument="Integrated\MongoDB\ContentType\Document\ContentType", cascade={"remove"})
     */
    protected $contentType;

    /**
     * @var bool One or more references possible
     * @ODM\Boolean
     */
    protected $multiple;

    /**
     * @var bool Is the reference required
     * @ODM\Boolean
     */
    protected $required;

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     * @return $this
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     * @return $this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }
    /**
     * @param bool $required
     * @return $this;
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }
}