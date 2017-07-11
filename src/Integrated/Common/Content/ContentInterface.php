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

use Integrated\Common\Content\Embedded\RelationInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 * @author Johnny Borg <johnny@e-active.nl>
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
     * Return the Slug of the content
     *
     * @return string
     */
    public function getSlug();
    
    /**
     * Set the slug of the Content
     *
     * @param string $slug
     * @return ContentInterface
     */
    public function setSlug($slug);

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
     * @return ContentInterface
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
     * @return RelationInterface|bool
     */
    public function getRelation($relationId);

    /**
     * Set the relations of the document
     *
     * @param Collection $relations
     * @return ContentInterface
     */
    public function setRelations(Collection $relations);

    /**
     * Add relation to relations collection
     *
     * @param RelationInterface $relation
     * @return ContentInterface
     */
    public function addRelation(RelationInterface $relation);

    /**
     * Remove relation from relations collection
     *
     * @param RelationInterface $relation
     * @return ContentInterface
     */
    public function removeRelation(RelationInterface $relation);
}
