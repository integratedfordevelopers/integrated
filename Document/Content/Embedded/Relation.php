<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\Content\ContentInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Embedded document Reference
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Relation
{
    /**
     * @var string
     * @ODM\String
     */
    protected $contentType;

    /**
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Content")
     */
    protected $references;

    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param Collection $references
     * @return $this
     */
    public function setReferences(Collection $references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    public function addReference(ContentInterface $reference)
    {
        $this->references->add($reference);
    }

    public function removeReference(ContentInterface $reference)
    {
        // TODO add remove function
        //$this->references->remove()
    }
}