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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * Set contentType of Relation
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get contentType of Relation
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set references of Relations
     * @param Collection $references
     * @return $this
     */
    public function setReferences(Collection $references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * Get references of Relation
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Add references to references collection
     *
     * @param Collection $references
     * @return $this
     */
    public function addReferences(Collection $references)
    {
        foreach ($references as $reference) {
            $this->addReference($reference);
        }

        return $this;
    }

    /**
     * Add reference to references collection
     * @param ContentInterface $reference
     * @return $this
     */
    public function addReference(ContentInterface $reference)
    {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
        }

        return $this;
    }

    /**
     * Remove reference from references collection
     * @param ContentInterface $reference
     */
    public function removeReference(ContentInterface $reference)
    {
        $this->references->removeElement($reference);
    }
}