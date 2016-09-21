<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
interface ContentInterface
{
	/**
	 * Return the id of the content
	 *
	 * @return string
	 */
	public function getId();

    /**
	 * Return the contentType of the Content
	 *
     * @return string
     */
    public function getContentType();

    /**
     * Set the contentType of the Content
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * Get the relations of the document
     *
     * @return ArrayCollection
     */
    public function getRelations();

    /**
     * @param $relationId
     * @return Relation|false
     */
    public function getRelation($relationId);

    /**
     * Set the relations of the document
     *
     * @param Collection $relations
     * @return $this
     */
    public function setRelations(Collection $relations);

    /**
     * Add relation to relations collection
     *
     * @param Relation $relation
     * @return $this
     */
    public function addRelation(Relation $relation);

    /**
     * Remove relation from relations collection
     *
     * @param Relation $relation
     * @return $this
     */
    public function removeRelation(Relation $relation);
}
