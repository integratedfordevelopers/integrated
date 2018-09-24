<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Bulk\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Bulk\BulkActionInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class RelationAction implements BulkActionInterface
{
    /**
     * @var string
     */
    private $handler;

    /**
     * @var Relation
     */
    private $relation;

    /**
     * @var ArrayCollection|ContentInterface[]
     */
    private $references;

    /**
     * RelationAction constructor.
     */
    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param Relation $relation
     *
     * @return $this
     */
    public function setRelation(Relation $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getReferences()
    {
        return $this->references->getIterator();
    }

    /**
     * @param ContentInterface[] $references
     *
     * @return $this
     */
    public function setReferences($references)
    {
        $this->references->clear();
        if (\is_array($references) || $references instanceof \Traversable) {
            foreach ($references as $reference) {
                $this->addReference($reference);
            }
        }

        return $this;
    }

    /**
     * @param ContentInterface $reference
     *
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
     * @param ContentInterface $reference
     *
     * @return $this
     */
    public function removeReference(ContentInterface $reference)
    {
        $this->references->removeElement($reference);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'relation' => $this->getRelation(),
            'references' => $this->getReferences(),
        ];
    }
}
