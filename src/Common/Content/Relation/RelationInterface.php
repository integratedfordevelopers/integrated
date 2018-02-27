<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Relation;

use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * Interface for Relation documents.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface RelationInterface
{
    /**
     * Returns the id of the Relation.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the type of the Relation.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the name of the Relation.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns a collection with ContentTypes.
     *
     * @return ContentTypeInterface[]
     */
    public function getSources();

    /**
     * Returns a collection with ContentTypes.
     *
     * @return ContentTypeInterface[]
     */
    public function getTargets();

    /**
     * Returns true if the Relation can have multiple references.
     *
     * @return bool
     */
    public function isMultiple();

    /**
     * Returns true if the Relation is required.
     *
     * @return bool
     */
    public function isRequired();
}
