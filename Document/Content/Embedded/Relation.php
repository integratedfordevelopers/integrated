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
use Doctrine\Common\Collections\Collection;
use Integrated\Common\Content\ContentInterface;

/**
 * Embedded document Reference
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Relation
{
    /**
     * @var string id of the ContentType\Embedded\Relation
     * @ODM\String
     * @ODM\Index
     */
    protected $contentTypeRelation;

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
     * @param string $contentTypeRelation
     * @return $this
     */
    public function setContentTypeRelation($contentTypeRelation)
    {
        $this->contentTypeRelation = $contentTypeRelation;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentTypeRelation()
    {
        return $this->contentTypeRelation;
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
     *
     * @param ContentInterface $reference
     * @return bool true if this collection contained the specified element, false otherwise.
     */
    public function removeReference(ContentInterface $reference)
    {
        return $this->references->removeElement($reference);
    }
}